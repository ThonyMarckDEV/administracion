<?php

namespace App\Services\Sunat;

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

        // Check if the directory exists
        if (!file_exists($certificatePath) || !is_dir($certificatePath)) {
            throw new InvalidArgumentException('Certificate directory not found at: ' . $certificatePath);
        }

        // Find .pem files in the directory
        $pemFiles = glob($certificatePath . '/*.pem');
        if (empty($pemFiles)) {
            throw new \Exception('No .pem files found in ' . $certificatePath);
        }

        // Use the first .pem file
        $certificateFile = $pemFiles[0];
        $certificateContent = file_get_contents($certificateFile);

        // Check if file_get_contents failed
        if ($certificateContent === false) {
            throw new \Exception('Failed to read certificate file: ' . $certificateFile);
        }

        $this->see = new See();
        $this->see->setCertificate($certificateContent);
        $this->see->setService(config('greenter.endpoint'));
        $this->see->setClaveSOL(
            config('greenter.ruc'),
            config('greenter.user'),
            config('greenter.password')
        );

        $this->pdfService = $pdfService;
    }
    
    /**
     * Generate a comprobante (Factura or Boleta) based on the provided data.
     *
     * @param array $data Validated request data
     * @param string $type Comprobante type ('factura' or 'boleta')
     * @return Invoice
     * @throws InvalidArgumentException
     */
    public function createComprobante(array $data, string $type): Invoice
    {
        if (!in_array($type, ['factura', 'boleta'])) {
            throw new InvalidArgumentException('Invalid comprobante type. Use "factura" or "boleta".');
        }

        // Client setup
        $client = $this->buildClient($data['client'], $type);

        // Company setup
        $company = $this->buildCompany($data['company']);

        // Invoice setup
        $invoice = $this->buildInvoice($data, $type, $client, $company);

        // Items setup
        $details = $this->buildSaleDetails($data['items']);

        // Legend setup
        $legend = (new Legend())
            ->setCode('1000')
            ->setValue($data['legend_value']);

        return $invoice->setDetails($details)->setLegends([$legend]);
    }

    /**
     * Send the comprobante to SUNAT, store XML, CDR, and generate PDF.
     *
     * @param Invoice $invoice
     * @param int $idPago
     * @return array
     * @throws \Exception
     */
    public function sendComprobante(Invoice $invoice, int $idPago): array
    {
        $docType = $invoice->getTipoDoc() === '01' ? 'facturas' : 'boletas';
        $pagoPath = "{$docType}/{$idPago}";
        $xmlPath = "{$pagoPath}/xml/{$invoice->getName()}.xml";
        $cdrPath = "{$pagoPath}/cdr/R-{$invoice->getName()}.zip";
        $pdfPath = "{$pagoPath}/pdf/{$invoice->getName()}.pdf";

        // Create directories
        Storage::makeDirectory("{$pagoPath}/xml");
        Storage::makeDirectory("{$pagoPath}/cdr");
        Storage::makeDirectory("{$pagoPath}/pdf");

        // Send to SUNAT
        $result = $this->see->send($invoice);
        Storage::put($xmlPath, $this->see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            throw new \Exception(
                'SUNAT Error: Code ' . ($result->getError()->getCode() ?? 'N/A') . ' - ' . ($result->getError()->getMessage() ?? 'No message')
            );
        }

        // Save CDR
        Storage::put($cdrPath, $result->getCdrZip());

        // Generate and save PDF
        $pdfAbsolutePath = $this->pdfService->generate($invoice, $pdfPath);

        return [
            'success' => $result->isSuccess(),
            'xml_path' => Storage::path($xmlPath),
            'cdr_path' => Storage::path($cdrPath),
            'pdf_path' => $pdfAbsolutePath,
            'cdr_status' => $this->processCdr($result->getCdrResponse()),
        ];
    }

    /**
     * Build the Client object.
     *
     * @param array $clientData
     * @param string $type
     * @return Client
     */
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

    /**
     * Build the Company object.
     *
     * @param array $companyData
     * @return Company
     */
    protected function buildCompany(array $companyData): Company
    {
        $address = (new Address())
            ->setUbigueo($companyData['address']['ubigueo'])
            ->setDepartamento($companyData['address']['departamento'])
            ->setProvincia($companyData['address']['provincia'])
            ->setDistrito($companyData['address']['distrito'])
            ->setUrbanizacion($companyData['address']['urbanizacion'])
            ->setDireccion($companyData['address']['direccion'])
            ->setCodLocal($companyData['address']['cod_local']);

        return (new Company())
            ->setRuc($companyData['ruc'])
            ->setRazonSocial($companyData['razon_social'])
            ->setNombreComercial($companyData['nombre_comercial'])
            ->setAddress($address);
    }

    /**
     * Build the Invoice object.
     *
     * @param array $data
     * @param string $type
     * @param Client $client
     * @param Company $company
     * @return Invoice
     */
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

    /**
     * Build SaleDetail objects from items data.
     *
     * @param array $items
     * @return array
     */
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

    /**
     * Process the CDR response from SUNAT.
     *
     * @param mixed $cdr
     * @return array
     */
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