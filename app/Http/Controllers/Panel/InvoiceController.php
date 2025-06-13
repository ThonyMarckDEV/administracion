<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Invoice::class);
        return Inertia::render('panel/invoice/indexInvoice');
    }

    /**
     * List invoices with pagination and search.
     */
    public function listarInvoices(Request $request)
    {
        Gate::authorize('viewAny', Invoice::class);
        try {
            $search = $request->get('search');
            $invoices = Invoice::with(['payment.customer', 'payment.paymentPlanService'])
                ->when($search, function ($query, $search) {
                    return $query->where('serie_assigned', 'like', "%$search%")
                                 ->orWhere('correlative_assigned', 'like', "%$search%")
                                 ->orWhereHas('payment.customer', function ($q) use ($search) {
                                     $q->where('name', 'like', "%$search%");
                                 })
                                 ->orWhereHas('payment.paymentPlanService', function ($q) use ($search) {
                                     $q->where('name', 'like', "%$search%");
                                 });
                })
                ->orderBy('id', 'asc')
                ->paginate(12);

            return response()->json([
                'invoices' => InvoiceResource::collection($invoices),
                'pagination' => [
                    'total' => $invoices->total(),
                    'current_page' => $invoices->currentPage(),
                    'per_page' => $invoices->perPage(),
                    'last_page' => $invoices->lastPage(),
                    'from' => $invoices->firstItem(),
                    'to' => $invoices->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los comprobantes',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);
        return response()->json([
            'status' => true,
            'message' => 'Comprobante encontrado',
            'invoice' => new InvoiceResource($invoice->load(['payment.customer', 'payment.paymentPlanService'])),
        ]);
    }

    /**
     * Annul the specified invoice (set sunat to 'anulado').
     */
    public function annul(Invoice $invoice)
    {
        Gate::authorize('delete', $invoice);
        if ($invoice->sunat === 'anulado') {
            return response()->json([
                'status' => false,
                'message' => 'El comprobante ya está anulado',
            ], 400);
        }

        $invoice->sunat = 'anulado';
        $invoice->save();

        return response()->json([
            'status' => true,
            'message' => 'Comprobante anulado correctamente',
        ]);
    }


    /**
     * Servir PDF para visualización.
     */
    public function viewPdf(Invoice $invoice, $payment_id): StreamedResponse
    {
        Gate::authorize('view', $invoice);

        // Validar que el payment_id coincide con el de la factura
        if ($invoice->payment_id != $payment_id) {
            abort(404, 'ID de pago no coincide con la factura');
        }

        // Obtener datos necesarios para generar el comprobante
       $payment = \App\Models\Payment::with(['customer', 'paymentPlan.service'])->findOrFail($payment_id);
        Log::info('Payment data for payment_id: ' . $payment_id, [
            'payment' => $payment->toArray(),
            'payment_plan' => $payment->paymentPlan ? $payment->paymentPlan->toArray() : null,
            'service' => $payment->paymentPlan && $payment->paymentPlan->service ? $payment->paymentPlan->service->toArray() : null,
        ]);
        $company = \App\Models\MyCompany::firstOrFail();

        // Instanciar el servicio de generación de comprobantes
        $pdfService = app(\App\Services\Sunat\GeneratePdf::class);
        $generateComprobante = new \App\Services\Sunat\GenerateComprobante($pdfService);

        // Construir datos para el comprobante
        $data = $this->buildComprobanteData($invoice, $payment, $company);
        $docType = $invoice->document_type === 'B' ? 'boleta' : 'factura';

        // Generar el objeto Invoice
        $greenterInvoice = $generateComprobante->createComprobante($data, $docType);

        // Generar el PDF en memoria (sin almacenar)
        $pdfContent = $pdfService->generate($greenterInvoice);

        if (!$pdfContent) {
            abort(404, 'No se pudo generar el PDF');
        }

        // Devolver el PDF como una respuesta streamed
        return new StreamedResponse(
            function () use ($pdfContent) {
                echo $pdfContent;
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $greenterInvoice->getName() . '.pdf"',
            ]
        );
    }

     protected function buildComprobanteData(Invoice $invoice, $payment, $company): array
    {
        $customer = $payment->customer;

        // Initialize service with fallback
        $service = (object)[
            'id' => 'N/A',
            'name' => 'Servicio no especificado',
        ];

        // Check payment plan and service
        if (!$payment->paymentPlan) {
            Log::error('Payment plan not loaded for payment_id: ' . ($payment->id ?? 'N/A'), [
                'payment_plan_id' => $payment->payment_plan_id ?? 'N/A',
            ]);
        } elseif (!$payment->paymentPlan->service) {
            Log::error('Service not loaded for payment_plan_id: ' . ($payment->payment_plan_id ?? 'N/A'), [
                'service_id' => $payment->paymentPlan->service_id ?? 'N/A',
            ]);
        } else {
            $service = $payment->paymentPlan->service;
            // Ensure valid service data
            if (!$service->id || !$service->name) {
                Log::warning('Invalid service data for payment_id: ' . $payment->id, [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                ]);
                $service->name = $service->name ?: 'Servicio sin nombre';
                $service->id = $service->id ?: 'N/A';
            }
        }

        // Log service data for debugging
        Log::info('Service data used for payment_id: ' . $payment->id, [
            'service_id' => $service->id,
            'service_name' => $service->name,
        ]);

        // Rest of the method remains unchanged
        $clientData = [
            'tipo_doc' => $invoice->document_type === 'B' ? ($customer->dni ? '1' : '0') : ($customer->ruc ? '6' : ($customer->dni ? '1' : '0')),
            'num_doc' => $invoice->document_type === 'B' ? ($customer->dni ?? '-') : ($customer->ruc ?? ($customer->dni ?? '-')),
            'razon_social' => $customer->name ?? 'CLIENTE NO ESPECIFICADO',
        ];

        if ($invoice->document_type === 'F' && $clientData['tipo_doc'] === '0') {
            Log::warning('Factura requires RUC or DNI for payment_id: ' . ($payment->id ?? 'N/A'), [
                'customer_id' => $customer->id ?? 'N/A',
                'dni' => $customer->dni ?? 'N/A',
                'ruc' => $customer->ruc ?? 'N/A',
            ]);
            $clientData['num_doc'] = '-';
        }

        $amount = $payment->amount ?? 0.00;
        $igvRate = 0.18;
        $mtoOperGravadas = round($amount / (1 + $igvRate), 2);
        $mtoIgv = round($amount - $mtoOperGravadas, 2);

        return [
            'client' => $clientData,
            'tipo_operacion' => '0101',
            'serie' => $invoice->serie_assigned,
            'correlativo' => $invoice->correlative_assigned,
            'fecha_emision' => now()->format('Y-m-d'),
            'tipo_moneda' => 'PEN',
            'mto_oper_gravadas' => $mtoOperGravadas,
            'mto_igv' => $mtoIgv,
            'total_impuestos' => $mtoIgv,
            'valor_venta' => $mtoOperGravadas,
            'sub_total' => $amount,
            'mto_imp_venta' => $amount,
            'legend_value' => 'SON ' . $this->numberToWords($amount) . ' SOLES',
            'items' => [
                [
                    'cod_producto' => $service->id,
                    'unidad' => 'NIU',
                    'cantidad' => 1,
                    'mto_valor_unitario' => $mtoOperGravadas,
                    'descripcion' => $service->name,
                    'mto_base_igv' => $mtoOperGravadas,
                    'porcentaje_igv' => $igvRate * 100,
                    'igv' => $mtoIgv,
                    'tip_afe_igv' => '10',
                    'total_impuestos' => $mtoIgv,
                    'mto_valor_venta' => $mtoOperGravadas,
                    'mto_precio_unitario' => $amount,
                ],
            ],
        ];
    }

    /**
     * Convertir número a texto (implementación básica).
     */
    protected function numberToWords($amount): string
    {
        // Separar parte entera y decimal
        $integerPart = floor($amount);
        $decimalPart = round(($amount - $integerPart) * 100);

        // Arrays para conversión básica
        $units = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $tens = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $teens = ['DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];

        $integerText = '';
        if ($integerPart == 0) {
            $integerText = 'CERO';
        } elseif ($integerPart < 10) {
            $integerText = $units[$integerPart];
        } elseif ($integerPart < 20) {
            $integerText = $teens[$integerPart - 10];
        } elseif ($integerPart < 100) {
            $ten = floor($integerPart / 10);
            $unit = $integerPart % 10;
            $integerText = $tens[$ten] . ($unit > 0 ? ' Y ' . $units[$unit] : '');
        } else {
            $integerText = number_format($integerPart, 0);
        }

        $decimalText = $decimalPart > 0 ? ' CON ' . ($decimalPart < 10 ? $units[$decimalPart] : $tens[floor($decimalPart / 10)] . ($decimalPart % 10 > 0 ? ' Y ' . $units[$decimalPart % 10] : '')) : '';

        return strtoupper($integerText . $decimalText . ' SOLES');
    }

    /**
     * Descargar archivo XML.
     */
    public function downloadXml(Invoice $invoice, $payment_id): StreamedResponse
    {
        Gate::authorize('view', $invoice);

        // Validar que el payment_id coincide con el de la factura
        if ($invoice->payment_id != $payment_id) {
            abort(404, 'ID de pago no coincide con la factura');
        }

        $docType = $invoice->document_type === 'B' ? 'boletas' : 'facturas';
        $folderPath = "{$docType}/{$payment_id}/xml";

        // Obtener archivos en la carpeta
        $files = Storage::disk('public')->files($folderPath);
        $xmlFile = array_filter($files, fn($file) => str_ends_with(strtolower($file), '.xml'));

        if (empty($xmlFile)) {
            abort(404, 'XML no encontrado');
        }

        $xmlPath = reset($xmlFile); // Obtener el primer archivo XML
        $fileName = basename($xmlPath);

        return Storage::disk('public')->download($xmlPath, $fileName, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Descargar archivo CDR (ZIP).
     */
    public function downloadCdr(Invoice $invoice, $payment_id): StreamedResponse
    {
        Gate::authorize('view', $invoice);

        // Validar que el payment_id coincide con el de la factura
        if ($invoice->payment_id != $payment_id) {
            abort(404, 'ID de pago no coincide con la factura');
        }

        $docType = $invoice->document_type === 'B' ? 'boletas' : 'facturas';
        $folderPath = "{$docType}/{$payment_id}/cdr";

        // Obtener archivos en la carpeta
        $files = Storage::disk('public')->files($folderPath);
        $zipFile = array_filter($files, fn($file) => str_ends_with(strtolower($file), '.zip'));

        if (empty($zipFile)) {
            abort(404, 'CDR no encontrado');
        }

        $cdrPath = reset($zipFile); // Obtener el primer archivo ZIP
        $fileName = basename($cdrPath);

        return Storage::disk('public')->download($cdrPath, $fileName, [
            'Content-Type' => 'application/zip',
        ]);
    }


}