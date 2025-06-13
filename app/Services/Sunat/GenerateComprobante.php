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
use Illuminate\Support\Facades\Log;
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

        Storage::disk('public')->makeDirectory("{$pagoPath}/xml");
        Storage::disk('public')->makeDirectory("{$pagoPath}/cdr");

        $result = $this->see->send($invoice);
        Storage::disk('public')->put($xmlPath, $this->see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            throw new \Exception(
                'SUNAT Error: Code ' . ($result->getError()->getCode() ?? 'N/A') . ' - ' . ($result->getError()->getMessage() ?? 'No message')
            );
        }

        Storage::disk('public')->put($cdrPath, $result->getCdrZip());
        $this->storeInvoice($invoice, $idPago, $result);

        return [
            'success' => $result->isSuccess(),
            'xml_path' => Storage::disk('public')->path($xmlPath),
            'cdr_path' => Storage::disk('public')->path($cdrPath),
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


    protected function buildInvoice(array $data, string $type, Client $client, Company $company): Invoice
    {
        // Validar datos monetarios
        $mto_oper_gravadas = (float) ($data['mto_oper_gravadas'] ?? 0);
        $mto_igv = (float) ($data['mto_igv'] ?? 0);
        $total_impuestos = (float) ($data['total_impuestos'] ?? 0);
        $valor_venta = (float) ($data['valor_venta'] ?? 0);
        $sub_total = (float) ($data['sub_total'] ?? 0);
        $mto_imp_venta = (float) ($data['mto_imp_venta'] ?? 0);

        // Validar consistencia
        if (abs($mto_oper_gravadas + $mto_igv - $mto_imp_venta) > 0.01) {
            Log::error('Inconsistencia en los totales del comprobante', [
                'mto_oper_gravadas' => $mto_oper_gravadas,
                'mto_igv' => $mto_igv,
                'mto_imp_venta' => $mto_imp_venta,
            ]);
            throw new \Exception('Los totales del comprobante no son consistentes');
        }

        return (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data['tipo_operacion'] ?? '0101') // Venta interna por defecto
            ->setTipoDoc($type === 'factura' ? '01' : '03')
            ->setSerie($data['serie'])
            ->setCorrelativo($data['correlativo'])
            ->setFechaEmision(new \DateTime($data['fecha_emision']))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda($data['tipo_moneda'] ?? 'PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($mto_oper_gravadas)
            ->setMtoIGV($mto_igv)
            ->setTotalImpuestos($total_impuestos)
            ->setValorVenta($valor_venta)
            ->setSubTotal($sub_total)
            ->setMtoImpVenta($mto_imp_venta);
    }


    protected function buildSaleDetails(array $items): array
    {
        return array_map(function ($itemData) {
            // Validar datos requeridos
            if (!isset($itemData['cantidad'], $itemData['mto_valor_unitario'], $itemData['porcentaje_igv'])) {
                throw new \Exception('Faltan datos requeridos en el ítem: cantidad, mto_valor_unitario o porcentaje_igv');
            }

            $cantidad = (float) $itemData['cantidad'];
            $mto_valor_unitario = (float) $itemData['mto_valor_unitario'];
            $porcentaje_igv = (float) ($itemData['porcentaje_igv'] ?? 18); // IGV por defecto 18%

            // Calcular mto_precio_unitario (incluye IGV)
            $mto_precio_unitario = round($mto_valor_unitario * (1 + $porcentaje_igv / 100), 2);

            // Calcular mto_base_igv
            $mto_base_igv = round($mto_valor_unitario * $cantidad, 2);

            // Calcular IGV
            $igv = round($mto_base_igv * ($porcentaje_igv / 100), 2);

            // Calcular total_impuestos
            $total_impuestos = $igv;

            // Calcular mto_valor_venta
            $mto_valor_venta = $mto_base_igv;

            // Log para depuración
            Log::debug('Cálculos del ítem', [
                'cod_producto' => $itemData['cod_producto'] ?? 'N/A',
                'cantidad' => $cantidad,
                'mto_valor_unitario' => $mto_valor_unitario,
                'mto_precio_unitario' => $mto_precio_unitario,
                'mto_base_igv' => $mto_base_igv,
                'igv' => $igv,
                'total_impuestos' => $total_impuestos,
                'mto_valor_venta' => $mto_valor_venta,
            ]);

            return (new SaleDetail())
                ->setCodProducto($itemData['cod_producto'] ?? '-')
                ->setUnidad($itemData['unidad'] ?? 'NIU')
                ->setCantidad($cantidad)
                ->setMtoValorUnitario($mto_valor_unitario)
                ->setDescripcion($itemData['descripcion'] ?? 'Servicio')
                ->setMtoBaseIgv($mto_base_igv)
                ->setPorcentajeIgv($porcentaje_igv)
                ->setIgv($igv)
                ->setTipAfeIgv($itemData['tip_afe_igv'] ?? '10') // Gravado - Operación Onerosa
                ->setTotalImpuestos($total_impuestos)
                ->setMtoValorVenta($mto_valor_venta)
                ->setMtoPrecioUnitario($mto_precio_unitario);
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