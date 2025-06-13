<?php

namespace App\Services\Sunat;

use App\Models\MyCompany;
use App\Models\Invoice;
use App\Models\VoidedDocument;
use App\Models\SeriesCorrelative;
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

    public function voidComprobante(array $data): array
    {
        $invoice = Invoice::with('payment')->findOrFail($data['invoice_id']);
        if (!$invoice->payment) {
            Log::error('Pago no encontrado para invoice_id: ' . $invoice->id, [
                'payment_id' => $invoice->payment_id,
            ]);
            throw new \Exception('La factura no tiene un pago asociado');
        }

        if ($invoice->sunat !== 'enviado') {
            throw new \Exception('La factura no está en un estado anulable (estado sunat: ' . $invoice->sunat . ')');
        }

        if (!$invoice->payment_id) {
            Log::error('payment_id no definido para invoice_id: ' . $invoice->id);
            throw new \Exception('La factura no tiene un payment_id asociado');
        }

        // Determinar docType
        $docType = match ($invoice->document_type) {
            'F' => 'facturas',
            'B' => 'boletas',
            default => throw new \Exception('Invalid document type: ' . $invoice->document_type),
        };

        // Validate document type
        $tipo_doc = match ($invoice->document_type) {
            'F' => '01', // Factura
            'B' => '03', // Boleta
            default => throw new \Exception('Invalid document type: ' . $invoice->document_type),
        };

        // Validar formato de serie
        if (!preg_match('/^[FB]\d{3}$/', $invoice->serie_assigned)) {
            Log::error('Formato de serie inválido', [
                'invoice_id' => $invoice->id,
                'serie' => $invoice->serie_assigned,
            ]);
            throw new \Exception('La serie asignada no cumple con el formato esperado (F/B seguido de 3 dígitos)');
        }

        // Validar consistencia entre tipo_doc y serie
        if (($tipo_doc === '01' && !str_starts_with($invoice->serie_assigned, 'F')) ||
            ($tipo_doc === '03' && !str_starts_with($invoice->serie_assigned, 'B'))) {
            Log::error('Inconsistencia entre tipo_doc y serie', [
                'invoice_id' => $invoice->id,
                'tipo_doc' => $tipo_doc,
                'serie' => $invoice->serie_assigned,
            ]);
            throw new \Exception('El tipo de documento no coincide con la serie asignada');
        }

        Log::debug('Datos completos para anulación', [
            'invoice_id' => $invoice->id,
            'document_type' => $invoice->document_type,
            'tipo_doc' => $tipo_doc,
            'serie' => $invoice->serie_assigned,
            'correlativo' => $invoice->correlative_assigned,
            'sunat_status' => $invoice->sunat,
            'payment_id' => $invoice->payment_id,
            'doc_type' => $docType,
        ]);

        $correlativo_baja = $this->generateCorrelativoBaja();
        $fec_generacion = $invoice->payment->created_at->format('Y-m-d');
        $fec_comunicacion = Carbon::today()->format('Y-m-d');

        // Validate voiding eligibility (e.g., within 7 days)
        $generationDate = Carbon::parse($fec_generacion);
        $communicationDate = Carbon::parse($fec_comunicacion);
        if ($generationDate->diffInDays($communicationDate) > 7) {
            throw new \Exception('Período de anulación excedido (más de 7 días desde la generación)');
        }

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

        // Generate correct filename for SUNAT (RUC-RA-YYYYMMDD-NNN)
        $ruc = $company->getRuc();
        $filename = "{$ruc}-RA-{$fec_comunicacion}-{$correlativo_baja}";
        $pagoPath = "{$docType}/{$invoice->payment_id}/voided/{$filename}";
        $xmlPath = "{$pagoPath}/xml/{$filename}.xml";
        $cdrPath = "{$pagoPath}/cdr/R-{$filename}.zip";

        Storage::disk('public')->makeDirectory("{$pagoPath}/xml");
        Storage::disk('public')->makeDirectory("{$pagoPath}/cdr");

        // Send to SUNAT
        Log::debug('Sending voided document to SUNAT', [
            'name' => $filename,
            'invoice_id' => $data['invoice_id'],
            'tipo_doc' => $tipo_doc,
            'correlativo_baja' => $correlativo_baja,
            'fec_generacion' => $fec_generacion,
            'fec_comunicacion' => $fec_comunicacion,
            'pago_path' => $pagoPath,
            'xml_path' => $xmlPath,
            'cdr_path' => $cdrPath,
        ]);

        $result = $this->see->send($voided);
        $xmlContent = $this->see->getFactory()->getLastXml();
        Storage::disk('public')->put($xmlPath, $xmlContent);
        Log::debug('Generated XML for voided document', [
            'invoice_id' => $data['invoice_id'],
            'xml_content' => $xmlContent,
            'xml_path' => $xmlPath,
        ]);

        if (!$result->isSuccess()) {
            $errorCode = $result->getError()->getCode() ?? 'N/A';
            $errorMessage = $result->getError()->getMessage() ?? 'No message';
            Log::error('SUNAT Send Error', [
                'code' => $errorCode,
                'message' => $errorMessage,
                'full_result' => (array) $result,
                'invoice_id' => $data['invoice_id'],
                'xml_content' => $xmlContent,
            ]);
            throw new \Exception("SUNAT Error: Code $errorCode - $errorMessage");
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
        Storage::disk('public')->put($cdrPath, $statusResult->getCdrZip());

        // Save voided document to database
        VoidedDocument::create([
            'invoice_id' => $invoice->id,
            'correlativo_baja' => "RA-{$fec_comunicacion}-{$correlativo_baja}",
            'fec_generacion' => $fec_generacion,
            'fec_comunicacion' => $fec_comunicacion,
            'motivo' => $data['motivo'],
            'xml_path' => $xmlPath,
            'cdr_path' => $cdrPath,
            'ticket' => $ticket,
            'status' => $statusResult->isSuccess() ? 'accepted' : 'rejected',
        ]);

        // Update invoice status
        $this->updateVoidedInvoice($invoice, $statusResult->getCdrResponse());

        return [
            'success' => $statusResult->isSuccess(),
            'ticket' => $ticket,
            'xml_path' => Storage::disk('public')->path($xmlPath),
            'cdr_path' => Storage::disk('public')->path($cdrPath),
            'cdr_status' => $this->processCdr($statusResult->getCdrResponse()),
        ];
    }

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

    protected function generateCorrelativoBaja(): string
    {
        $date = Carbon::today()->format('Ymd'); // Formato YYYYMMDD
        $serie = "RA{$date}"; // Ejemplo: RA20250613

        // Buscar o crear el registro en series_correlatives
        $seriesCorrelative = SeriesCorrelative::firstOrCreate(
            [
                'document_type' => 'RA',
                'serie' => $serie,
            ],
            [
                'correlative' => 0,
            ]
        );

        // Incrementar el correlativo
        $seriesCorrelative->increment('correlative');
        $newCorrelative = $seriesCorrelative->correlative;

        // Formatear el correlativo con 3 dígitos (001, 002, etc.)
        return str_pad($newCorrelative, 3, '0', STR_PAD_LEFT); // Ejemplo: 001
    }

    protected function buildCompany(): Company
    {
        $companyData = MyCompany::first();

        if (!$companyData) {
            throw new \Exception('No company record found in mycompany table.');
        }

        if (empty($companyData->ubigueo) || !preg_match('/^\d{6}$/', $companyData->ubigueo)) {
            Log::error('Código UBIGEO inválido', ['ruc' => $companyData->ruc, 'ubigeo' => $companyData->ubigueo]);
            throw new \Exception('El código UBIGEO del emisor no es válido');
        }

        $address = (new Address())
            ->setUbigueo($companyData->ubigueo)
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
        $errorMessages = [
            2308 => 'Tipo de documento inválido. Verifique que el comprobante esté registrado en SUNAT con el tipo correcto.',
            4093 => 'Código UBIGEO inválido. Actualice los datos del emisor en la tabla mycompany.',
            4287 => 'Error en los cálculos del precio unitario. Verifique los valores monetarios del comprobante.',
        ];

        $status = [
            'code' => $code,
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes() ?? [],
            'user_message' => $errorMessages[$code] ?? 'Error desconocido en SUNAT',
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