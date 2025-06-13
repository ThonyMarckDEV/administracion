<?php

namespace App\Services\Sunat;

use App\Models\MyCompany;
use App\Models\Invoice;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\See;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Carbon\Carbon;

class VoidComprobante
{
    protected $see;

    public function __construct()
    {
        $certificatePath = config('greenter.certificate_path');

        if (!file_exists($certificatePath) || !is_dir($certificatePath)) {
            throw new InvalidArgumentException('Certificate directory not found at: ' . $certificatePath);
        }

        $pemFiles = glob($certificatePath . '/*.pem');
        if (empty($pemFiles)) {
            throw new \Exception('No .pem files found in ' . $certificatePath);
        }

        $certificateFile = $pemFiles[0];
        $certificateContent = file_get_contents($certificateFile);

        if ($certificateContent === false) {
            throw new \Exception('Failed to read certificate file: ' . $certificateFile);
        }

        $this->see = new See();
        $this->see->setCertificate($certificateContent);
        $this->see->setService(config('greenter.endpoint'));

        $company = MyCompany::first();
        if (!$company) {
            throw new \Exception('No company record found in mycompany table.');
        }

        $this->see->setClaveSOL(
            $company->ruc,
            config('greenter.user'),
            config('greenter.password')
        );
    }

    /**
     * Void a comprobante using invoice ID and motivo.
     *
     * @param array $data Array containing: invoice_id, motivo
     * @return array
     * @throws \Exception
     */
    public function voidComprobante(array $data): array
    {
        $invoice = Invoice::with('payment')->findOrFail($data['invoice_id']);

        if ($invoice->sunat !== 'enviado') {
            throw new \Exception('Invoice is not in a voidable state (sunat status: ' . $invoice->sunat . ')');
        }

        // Validate and map document type
        $tipo_doc = match ($invoice->document_type) {
            'F' => '01', // Factura
            'B' => '03', // Boleta
            default => throw new \Exception('Invalid document type: ' . $invoice->document_type),
        };

        Log::debug('Preparing to void invoice', [
            'invoice_id' => $invoice->id,
            'document_type' => $invoice->document_type,
            'tipo_doc' => $tipo_doc,
            'serie' => $invoice->serie_assigned,
            'correlativo' => $invoice->correlative_assigned,
        ]);

        $correlativo_baja = $this->generateCorrelativoBaja();
        $fec_generacion = $invoice->payment->created_at->format('Y-m-d');
        $fec_comunicacion = Carbon::today()->format('Y-m-d');

        $company = $this->buildCompany();
        $voided = new Voided();
        $voided->setCorrelativo($correlativo_baja)
            ->setFecGeneracion(new \DateTime($fec_generacion))
            ->setFecComunicacion(new \DateTime($fec_comunicacion))
            ->setCompany($company);

        $details = [
            (new VoidedDetail())
                ->setTipoDoc($tipo_doc)
                ->setSerie($invoice->serie_assigned)
                ->setCorrelativo($invoice->correlative_assigned)
                ->setDesMotivoBaja($data['motivo'])
        ];

        $voided->setDetails($details);

        $pagoPath = "voided/{$voided->getName()}";
        $xmlPath = "{$pagoPath}/xml/{$voided->getName()}.xml";
        $cdrPath = "{$pagoPath}/cdr/R-{$voided->getName()}.zip";

        Storage::makeDirectory("{$pagoPath}/xml");
        Storage::makeDirectory("{$pagoPath}/cdr");

        // Send to SUNAT
        Log::debug('Sending voided document to SUNAT', [
            'name' => $voided->getName(),
            'invoice_id' => $data['invoice_id'],
            'tipo_doc' => $tipo_doc,
            'correlativo_baja' => $correlativo_baja,
            'fec_generacion' => $fec_generacion,
            'fec_comunicacion' => $fec_comunicacion,
        ]);

        $result = $this->see->send($voided);
        $xmlContent = $this->see->getFactory()->getLastXml();
        Storage::put($xmlPath, $xmlContent);
        Log::debug('Generated XML', ['xml' => $xmlContent]);

        if (!$result->isSuccess()) {
            Log::error('SUNAT Send Error', [
                'code' => $result->getError()->getCode() ?? 'N/A',
                'message' => $result->getError()->getMessage() ?? 'No message',
            ]);
            throw new \Exception(
                'SUNAT Error: Code ' . ($result->getError()->getCode() ?? 'N/A') . ' - ' . ($result->getError()->getMessage() ?? 'No message')
            );
        }

        $ticket = $result->getTicket();
        Log::debug('SUNAT Send Success', ['ticket' => $ticket]);

        // Check status
        $statusResult = $this->see->getStatus($ticket);
        if (!$statusResult->isSuccess()) {
            Log::error('SUNAT Status Error', [
                'ticket' => $ticket,
                'code' => $statusResult->getError()->getCode() ?? 'N/A',
                'message' => $statusResult->getError()->getMessage() ?? 'No message',
            ]);
            throw new \Exception(
                'SUNAT Status Error: Code ' . ($statusResult->getError()->getCode() ?? 'N/A') . ' - ' . ($statusResult->getError()->getMessage() ?? 'No message')
            );
        }

        Log::debug('SUNAT Status Success', ['ticket' => $ticket, 'cdr_response' => $statusResult->getCdrResponse()->getDescription()]);
        Storage::put($cdrPath, $statusResult->getCdrZip());

        // Update invoice status
        $this->updateVoidedInvoice($invoice, $statusResult->getCdrResponse());

        return [
            'success' => $statusResult->isSuccess(),
            'ticket' => $ticket,
            'xml_path' => Storage::path($xmlPath),
            'cdr_path' => Storage::path($cdrPath),
            'cdr_status' => $this->processCdr($statusResult->getCdrResponse()),
        ];
    }

    /**
     * Update the invoice status based on SUNAT response.
     *
     * @param Invoice $invoice
     * @param mixed $cdrResponse
     * @return void
     */
    protected function updateVoidedInvoice(Invoice $invoice, $cdrResponse): void
    {
        $cdrStatus = $this->processCdr($cdrResponse);

        if ($cdrStatus['estado'] === 'ACCEPTED') {
            Log::debug('Updating invoice status to anulado', [
                'invoice_id' => $invoice->id,
                'serie' => $invoice->serie_assigned,
                'correlativo' => $invoice->correlative_assigned,
                'document_type' => $invoice->document_type,
            ]);
            $invoice->update(['sunat' => 'anulado']);
        }
    }

    /**
     * Generate a correlativo_baja for the voided document.
     *
     * @return string
     */
    protected function generateCorrelativoBaja(): string
    {
        // Simple implementation: use a fixed value or increment
        // You can enhance this with a database counter or unique logic
        return '1'; // Adjust based on your requirements
    }

    protected function buildCompany(): Company
    {
        $companyData = MyCompany::first();

        if (!$companyData) {
            throw new \Exception('No company record found in mycompany table.');
        }

        $address = (new Address())
            ->setUbigueo($companyData->ubigeo)
            ->setDepartamento($companyData->departamento)
            ->setProvincia($companyData->provincia)
            ->setDistrito($companyData->distrito)
            ->setUrbanizacion($companyData->urbanizacion)
            ->setDireccion($companyData->direccion)
            ->setCodLocal($companyData->cod_local);

        return (new Company())
            ->setRuc($companyData->ruc)
            ->setRazonSocial($companyData->razon_social)
            ->setNombreComercial($companyData->nombre_comercial)
            ->setAddress($address);
    }

    protected function processCdr($cdr): array
    {
        $code = (int) $cdr->getCode();
        $status = [
            'code' => $code,
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes() ?? [],
        ];

        if ($code === 0) {
            $status['estado'] = 'ACCEPTED';
        } elseif ($code >= 2000 && $code <= 3999) {
            $status['estado'] = 'REJECTED';
        } else {
            $status['estado'] = 'EXCEPTION';
        }

        return $status;
    }
}