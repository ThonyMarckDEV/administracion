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

class ComprobanteService
{
    protected $see;

    public function __construct()
    {
        if (!file_exists(config('greenter.certificate_path'))) {
            throw new \Exception('Certificate not found at: ' . config('greenter.certificate_path'));
        }

        $this->see = new See();
        $this->see->setCertificate(file_get_contents(config('greenter.certificate_path')));
        $this->see->setService(config('greenter.endpoint'));
        $this->see->setClaveSOL(
            config('greenter.ruc'),
            config('greenter.user'),
            config('greenter.password')
        );
    }

    public function createFactura(array $data): Invoice
    {
        // Client
        $client = (new Client())
            ->setTipoDoc($data['client']['tipo_doc'])
            ->setNumDoc($data['client']['num_doc'])
            ->setRznSocial($data['client']['razon_social']);

        // Company Address
        $address = (new Address())
            ->setUbigueo($data['company']['address']['ubigueo'])
            ->setDepartamento($data['company']['address']['departamento'])
            ->setProvincia($data['company']['address']['provincia'])
            ->setDistrito($data['company']['address']['distrito'])
            ->setUrbanizacion($data['company']['address']['urbanizacion'])
            ->setDireccion($data['company']['address']['direccion'])
            ->setCodLocal($data['company']['address']['cod_local']);

        // Company
        $company = (new Company())
            ->setRuc($data['company']['ruc'])
            ->setRazonSocial($data['company']['razon_social'])
            ->setNombreComercial($data['company']['nombre_comercial'])
            ->setAddress($address);

        // Invoice
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data['tipo_operacion'])
            ->setTipoDoc('01') // Factura
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

        // Items
        $details = [];
        foreach ($data['items'] as $itemData) {
            $item = (new SaleDetail())
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
            $details[] = $item;
        }

        // Legend
        $legend = (new Legend())
            ->setCode('1000')
            ->setValue($data['legend_value']);

        $invoice->setDetails($details)->setLegends([$legend]);

        return $invoice;
    }

    public function createBoleta(array $data): Invoice
    {
        // Client (less strict for boletas)
        $client = (new Client())
            ->setTipoDoc($data['client']['tipo_doc'] ?? '0') // '0' for anonymous
            ->setNumDoc($data['client']['num_doc'] ?? '-')
            ->setRznSocial($data['client']['razon_social'] ?? 'CLIENTE VARIOS');

        // Company Address
        $address = (new Address())
            ->setUbigueo($data['company']['address']['ubigueo'])
            ->setDepartamento($data['company']['address']['departamento'])
            ->setProvincia($data['company']['address']['provincia'])
            ->setDistrito($data['company']['address']['distrito'])
            ->setUrbanizacion($data['company']['address']['urbanizacion'])
            ->setDireccion($data['company']['address']['direccion'])
            ->setCodLocal($data['company']['address']['cod_local']);

        // Company
        $company = (new Company())
            ->setRuc($data['company']['ruc'])
            ->setRazonSocial($data['company']['razon_social'])
            ->setNombreComercial($data['company']['nombre_comercial'])
            ->setAddress($address);

        // Boleta
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data['tipo_operacion'])
            ->setTipoDoc('03') // Boleta
            ->setSerie($data['serie']) // Typically starts with 'B' (e.g., B001)
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

        // Items
        $details = [];
        foreach ($data['items'] as $itemData) {
            $item = (new SaleDetail())
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
            $details[] = $item;
        }

        // Legend
        $legend = (new Legend())
            ->setCode('1000')
            ->setValue($data['legend_value']);

        $invoice->setDetails($details)->setLegends([$legend]);

        return $invoice;
    }

    public function sendComprobante(Invoice $invoice, int $idPago): array
    {
        // Determine directory based on document type
        $docType = $invoice->getTipoDoc() === '01' ? 'facturas' : 'boletas';
        
        // Define base paths
        $basePath = config('greenter.storage_path');
        $docPath = $docType;
        $pagoPath = "{$docPath}/{$idPago}";
        $xmlPath = "{$pagoPath}/xml/{$invoice->getName()}.xml";
        $cdrPath = "{$pagoPath}/cdr/R-{$invoice->getName()}.zip";

        // Create directories if they don't exist
        Storage::makeDirectory($docPath);
        Storage::makeDirectory("{$pagoPath}/xml");
        Storage::makeDirectory("{$pagoPath}/cdr");

        // Send to SUNAT
        $result = $this->see->send($invoice);

        // Save XML
        Storage::put($xmlPath, $this->see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            throw new \Exception(
                'SUNAT Error: Code ' . ($result->getError()->getCode() ?? 'N/A') . ' - ' . ($result->getError()->getMessage() ?? 'No message')
            );
        }

        // Save CDR
        Storage::put($cdrPath, $result->getCdrZip());

        // Process CDR
        $cdr = $result->getCdrResponse();
        $status = $this->processCdr($cdr);

        return [
            'success' => $result->isSuccess(),
            'xml_path' => Storage::path($xmlPath),
            'cdr_path' => Storage::path($cdrPath),
            'cdr_status' => $status,
        ];
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