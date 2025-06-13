<?php

namespace App\Services\Sunat;

use App\Models\MyCompany;
use App\Models\Invoice as InvoiceModel;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class GenerateComprobante
{
    protected $see;
    protected $pdfService;

    public function __construct(GeneratePdf $pdfService)
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

        $this->pdfService = $pdfService;
    }

    public function createComprobante(array $data, string $type): Invoice
    {
        if (!in_array($type, ['factura', 'boleta'])) {
            throw new InvalidArgumentException('Invalid comprobante type. Use "factura" or "boleta".');
        }

        $client = $this->buildClient($data['client'], $type);
        $company = $this->buildCompany();
        $invoice = $this->buildInvoice($data, $type, $client, $company);
        $details = $this->buildSaleDetails($data['items']);
        $legend = (new Legend())
            ->setCode('1000')
            ->setValue($data['legend_value']);

        return $invoice->setDetails($details)->setLegends([$legend]);
    }

    public function sendComprobante(Invoice $invoice, int $idPago): array
    {
        $docType = $invoice->getTipoDoc() === '01' ? 'facturas' : 'boletas';
        $pagoPath = "{$docType}/{$idPago}";
        $xmlPath = "{$pagoPath}/xml/{$invoice->getName()}.xml";
        $cdrPath = "{$pagoPath}/cdr/R-{$invoice->getName()}.zip";
        $pdfPath = "{$pagoPath}/pdf/{$invoice->getName()}.pdf";

        Storage::disk('public')->makeDirectory("{$pagoPath}/xml");
        Storage::disk('public')->makeDirectory("{$pagoPath}/cdr");
        Storage::disk('public')->makeDirectory("{$pagoPath}/pdf");

        $result = $this->see->send($invoice);
        Storage::disk('public')->put($xmlPath, $this->see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            throw new \Exception(
                'SUNAT Error: Code ' . ($result->getError()->getCode() ?? 'N/A') . ' - ' . ($result->getError()->getMessage() ?? 'No message')
            );
        }

        Storage::disk('public')->put($cdrPath, $result->getCdrZip());
        $pdfAbsolutePath = $this->pdfService->generate($invoice, $pdfPath);
        $this->storeInvoice($invoice, $idPago, $result);

        return [
            'success' => $result->isSuccess(),
            'xml_path' => Storage::disk('public')->path($xmlPath),
            'cdr_path' => Storage::disk('public')->path($cdrPath),
            'pdf_path' => Storage::disk('public')->path($pdfAbsolutePath),
            'cdr_status' => $this->processCdr($result->getCdrResponse()),
        ];
    }

    protected function storeInvoice(Invoice $invoice, int $idPago, $result): void
    {
        $cdrStatus = $this->processCdr($result->getCdrResponse());
        $documentType = $invoice->getTipoDoc() === '01' ? 'F' : 'B'; // 'F' for factura, 'B' for boleta

        InvoiceModel::create([
            'payment_id' => $idPago,
            'document_type' => $documentType,
            'serie_assigned' => $invoice->getSerie(),
            'correlative_assigned' => $invoice->getCorrelativo(),
            'sunat' => $cdrStatus['estado'] === 'ACCEPTED' ? 'enviado' : 'anulado',
        ]);
    }

    protected function buildClient(array $clientData, string $type): Client
    {
        $client = new Client();

        if ($type === 'boleta') {
            return $client
                ->setTipoDoc($clientData['tipo_doc'] ?? '0')
                ->setNumDoc($clientData['num_doc'] ?? '-')
                ->setRznSocial($clientData['razon_social'] ?? 'CLIENTE VARIOS');
        }

        return $client
            ->setTipoDoc($clientData['tipo_doc'])
            ->setNumDoc($clientData['num_doc'])
            ->setRznSocial($clientData['razon_social']);
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

    protected function buildInvoice(array $data, string $type, Client $client, Company $company): Invoice
    {
        return (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data['tipo_operacion'])
            ->setTipoDoc($type === 'factura' ? '01' : '03')
            ->setSerie($data['serie'])
            ->setCorrelativo($data['correlativo'])
            ->setFechaEmision(new \DateTime($data['fecha_emision']))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda($data['tipo_moneda'])
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($data['mto_oper_gravadas'])
            ->setMtoIGV($data['mto_igv'])
            ->setTotalImpuestos($data['total_impuestos'])
            ->setValorVenta($data['valor_venta'])
            ->setSubTotal($data['sub_total'])
            ->setMtoImpVenta($data['mto_imp_venta']);
    }

    protected function buildSaleDetails(array $items): array
    {
        return array_map(function ($itemData) {
            return (new SaleDetail())
                ->setCodProducto($itemData['cod_producto'])
                ->setUnidad($itemData['unidad'])
                ->setCantidad($itemData['cantidad'])
                ->setMtoValorUnitario($itemData['mto_valor_unitario'])
                ->setDescripcion($itemData['descripcion'])
                ->setMtoBaseIgv($itemData['mto_base_igv'])
                ->setPorcentajeIgv($itemData['porcentaje_igv'])
                ->setIgv($itemData['igv'])
                ->setTipAfeIgv($itemData['tip_afe_igv'])
                ->setTotalImpuestos($itemData['total_impuestos'])
                ->setMtoValorVenta($itemData['mto_valor_venta'])
                ->setMtoPrecioUnitario($itemData['mto_precio_unitario']);
        }, $items);
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