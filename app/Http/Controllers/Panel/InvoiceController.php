<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        $docType = $invoice->document_type === 'B' ? 'boletas' : 'facturas';
        $folderPath = "{$docType}/{$payment_id}/pdf";

        // Obtener archivos en la carpeta
        $files = Storage::disk('public')->files($folderPath);
        $pdfFile = array_filter($files, fn($file) => str_ends_with(strtolower($file), '.pdf'));

        if (empty($pdfFile)) {
            abort(404, 'PDF no encontrado');
        }

        $pdfPath = reset($pdfFile); // Obtener el primer archivo PDF

        return Storage::disk('public')->response($pdfPath, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($pdfPath) . '"',
        ]);
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