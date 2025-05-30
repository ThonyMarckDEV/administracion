<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\SeriesCorrelative;
use App\Models\ClientType;
use App\Services\Sunat\GenerateComprobante;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentDocumentService
{
    protected $comprobanteService;

    public function __construct(GenerateComprobante $comprobanteService)
    {
        $this->comprobanteService = $comprobanteService;
    }

    public function generateDocument(Payment $payment): array
    {
        // Fetch related data
        $customer = $payment->customer;
        $clientType = $customer ? $customer->clienteType : null;
        $paymentPlan = $payment->paymentPlan;
        $service = $paymentPlan ? $paymentPlan->service : null;

        // Validate related data
        if (!$customer) {
            Log::error('Customer not found for payment', ['payment_id' => $payment->id]);
            throw new \Exception('Customer not found for payment ID: ' . $payment->id);
        }

        // Debug clientType
        $rawClientType = ClientType::find($customer->client_type_id);
        Log::debug('Client type query', [
            'customer_id' => $customer->id,
            'client_type_id' => $customer->client_type_id,
            'clientType' => $clientType ? $clientType->toArray() : null,
            'rawClientType' => $rawClientType ? $rawClientType->toArray() : null,
        ]);

        if (!$clientType) {
            Log::error('Client type not found for customer', [
                'payment_id' => $payment->id,
                'customer_id' => $customer->id,
                'client_type_id' => $customer->client_type_id,
                'rawClientType' => $rawClientType ? $rawClientType->toArray() : null,
            ]);
            $documentType = 'B'; // Default to boleta
            Log::warning('Using default document type (Boleta) due to missing client type', [
                'customer_id' => $customer->id,
            ]);
        } else {
            $documentType = $clientType->name === 'Empresa' ? 'F' : 'B';
        }

        if (!$paymentPlan || !$service) {
            Log::error('Payment plan or service not found', [
                'payment_id' => $payment->id,
                'payment_plan_id' => $payment->payment_plan_id,
                'service_id' => $paymentPlan ? $paymentPlan->service_id : null,
            ]);
            throw new \Exception('Payment plan or service not found for payment ID: ' . $payment->id);
        }

        // Fetch series and correlative
        $seriesCorrelative = SeriesCorrelative::where('document_type', $documentType)->first();

        if (!$seriesCorrelative) {
            Log::error('Series not found for document type', [
                'document_type' => $documentType,
                'payment_id' => $payment->id,
            ]);
            throw new \Exception("No series found for document type: {$documentType}");
        }

        // Calculate amounts (amount includes 18% IGV)
        $mtoOperGravadas = round($payment->amount / 1.18, 2); // Base amount (152.54)
        $mtoIgv = round($payment->amount - $mtoOperGravadas, 2); // IGV (27.46)
        $mtoImpVenta = $payment->amount; // Total with IGV (180.00)
        $mtoPrecioUnitario = $documentType === 'F' ? round($mtoOperGravadas * 1.18, 2) : $mtoOperGravadas; // IGV-inclusive for facturas

        // Format series and correlative for SUNAT
        $serie = $documentType === 'B' ? 'B' . $seriesCorrelative->serie : 'F' . $seriesCorrelative->serie;
        $correlativo = (string) ($seriesCorrelative->correlative + 1); // No padding

        // Generate document number
        $documentNumber = "{$serie}-{$correlativo}";

        // Build the JSON structure
        $documentData = [
            'id_pago' => $payment->id,
            'client' => [
                'tipo_doc' => $clientType && $clientType->name === 'Empresa' ? '6' : '1',
                'num_doc' => $clientType && $clientType->name === 'Empresa' ? $customer->ruc : $customer->dni,
                'razon_social' => $customer->name,
            ],
            'tipo_operacion' => '0101',
            'tipo_comprobante' => $documentType === 'B' ? '03' : '01',
            'serie' => $serie,
            'correlativo' => $correlativo,
            'fecha_emision' => Carbon::parse($payment->payment_date)->toIso8601String(),
            'tipo_moneda' => 'PEN',
            'mto_oper_gravadas' => $mtoOperGravadas,
            'mto_igv' => $mtoIgv,
            'total_impuestos' => $mtoIgv,
            'valor_venta' => $mtoOperGravadas,
            'sub_total' => $mtoImpVenta,
            'mto_imp_venta' => $mtoImpVenta,
            'legend_value' => $this->numberToWords($mtoImpVenta),
            'items' => [
                [
                    'cod_producto' => 'P' . str_pad($service->id, 3, '0', STR_PAD_LEFT),
                    'unidad' => 'NIU',
                    'cantidad' => 1,
                    'mto_valor_unitario' => $mtoOperGravadas,
                    'descripcion' => $service->name,
                    'mto_base_igv' => $mtoOperGravadas,
                    'porcentaje_igv' => 18.0,
                    'igv' => $mtoIgv,
                    'tip_afe_igv' => '10',
                    'total_impuestos' => $mtoIgv,
                    'mto_valor_venta' => $mtoOperGravadas,
                    'mto_precio_unitario' => $mtoPrecioUnitario,
                ],
            ],
        ];

        // Process the comprobante
        try {
            $type = $documentType === 'F' ? 'factura' : 'boleta';
            Log::debug('Calling GenerateComprobante', [
                'payment_id' => $payment->id,
                'document_type' => $documentType,
                'payload' => $documentData,
            ]);

            $comprobante = $this->comprobanteService->createComprobante($documentData, $type);
            $result = $this->comprobanteService->sendComprobante($comprobante, $payment->id);

            $seriesCorrelative->increment('correlative');
            Log::info('Document generated successfully', [
                'payment_id' => $payment->id,
                'document_type' => $documentType,
                'document_number' => $documentNumber,
                'result' => $result,
            ]);
            return $documentData;
        } catch (\Exception $e) {
            Log::error('GenerateComprobante exception', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);
            throw new \Exception('Failed to generate document: ' . $e->getMessage());
        }
    }

    private function numberToWords(float $amount): string
    {
        $number = number_format($amount, 2, '.', '');
        return "SON " . strtoupper($number) . " CON 00/100 SOLES";
    }
}