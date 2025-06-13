<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

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
}