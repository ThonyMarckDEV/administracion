<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Jobs\SendPdfEmailJob;
use App\Jobs\SendNotificationPayFalseJob;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Exports\PaymentExport;
use App\Services\Payment\PaymentDocumentService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class PaymentController extends Controller
{
    protected $paymentDocumentService;

    public function __construct(PaymentDocumentService $paymentDocumentService)
    {
        $this->paymentDocumentService = $paymentDocumentService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Payment::class);
        return Inertia::render('panel/payment/indexPayment');
    }


    public function listPayments(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);
    try {
        $customer = $request->get('customer');
        $status = $request->get('status');

        $payments = Payment::with(['customer', 'paymentPlan', 'discount'])
            ->when($customer, function ($query, $customer) {
                $query->whereHas('customer', function ($query) use ($customer) {
                    $query->where('name', 'like', "%$customer%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'asc')
            ->paginate(12);

        return response()->json([
            'payments' => PaymentResource::collection($payments),
            'pagination' => [
                'total' => $payments->total(),
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'last_page' => $payments->lastPage(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem()
            ]
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Error al listar los pagos',
            'error' => $th->getMessage()
        ], 500);
    }
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $payments_plan = PaymentPlan::select('id', 'name')
            ->where('state', 1)
            ->get();
        $discounts = Discount::select('id', 'description')->where('state', 1)
            ->get();
        return Inertia::render('panel/payment/createPayment', [
            'payments_plan' => $payments_plan,
            'discounts' => $discounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        Gate::authorize('create', Payment::class);
        $validates = Payment::create($request->validated());
        return redirect()->route('panel.payments.index')->with([
            'status' => true,
            'message' => 'Pago creado correctamente',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        Gate::authorize('view', $payment);
        return response()->json([
            'status' => true,
            'message' => 'Pago encontrado',

            'payment' => new PaymentResource($payment),
        ]);
    }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(UpdatePaymentRequest $request, Payment $payment)
    // {
    //     Gate::authorize('update', $payment);
    //     $data = $request->validated();
    //     $originalFields = ['customer_id', 'payment_plan_id', 'discount_id'];
    //     foreach ($originalFields as $field) {
    //         if (($data[$field] ?? null) === null) {
    //             $data[$field] = $payment->{$field};
    //         }
    //     }
    //     $payment->update($data);
    //     if (($data['service_id'] ?? null) !== null && $payment->paymentPlan) {
    //         Log::info('Actualizando service_id', ['service_id' => $data['service_id']]);
    //         $payment->paymentPlan->update(['service_id' => $data['service_id']]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Pago actualizado correctamente',
    //         'payment' => new PaymentResource($payment),
    //     ]);
    // }
    
  /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        Gate::authorize('update', $payment);

        $data = $request->validated();
        $originalFields = ['customer_id', 'payment_plan_id', 'discount_id'];

        foreach ($originalFields as $field) {
            if (($data[$field] ?? null) === null) {
                $data[$field] = $payment->{$field};
            }
        }

        $wasPendingOrVencido = in_array($payment->status, ['pendiente', 'vencido']);
        $isNowPagado = $data['status'] === 'pagado';

        $payment->update($data);

        if (($data['service_id'] ?? null) !== null && $payment->paymentPlan) {
            Log::info('Actualizando service_id', ['service_id' => $data['service_id']]);
            $payment->paymentPlan->update(['service_id' => $data['service_id']]);
        }

        $responseData = [
            'status' => true,
            'message' => 'Pago actualizado correctamente',
            'payment' => new PaymentResource($payment),
        ];

        // ✅ Generar documento y enviar PDF si el estado cambia a "pagado"
        if ($wasPendingOrVencido && $isNowPagado) {
            try {
                $documentData = $this->paymentDocumentService->generateDocument($payment);
                $responseData['document'] = $documentData;

           $pdfPath = $documentData['pdf_path'] ?? null;

Log::debug('Ruta PDF recibida del documentData', [
    'pdf_path' => $pdfPath,
    'all_data' => $documentData,
]);

if ($pdfPath) {
    $relativePdfPath = str_replace(Storage::disk('public')->path(''), '', $pdfPath);

if (Storage::disk('public')->exists($relativePdfPath)) {
    $correoDestino = 'briamrebaza@hotmail.com';
    SendPdfEmailJob::dispatch($pdfPath, $correoDestino);
    Log::info('Se envió el Job para enviar el PDF por correo.', ['correo' => $correoDestino]);
    } else {
        Log::warning('PDF no encontrado por Storage::exists.', [
            'relative_path' => $relativePdfPath,
            'full_path' => $pdfPath,
        ]);
    }
} else {
    Log::warning('PDF path no proporcionado.', [
        'payment_id' => $payment->id,
        'document_data_keys' => array_keys($documentData),
    ]);
}

        } catch (\Exception $e) {
            Log::error('Error al generar o enviar el comprobante PDF.', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Error al generar el documento: ' . $e->getMessage(),
            ], 500);
        }
    }

    if ($data['status'] === 'vencido') {
        SendNotificationPayFalseJob::dispatch('briamrebaza@hotmail.com');
    }

        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        Gate::authorize('delete', $payment);
        $payment->delete();
        return response()->json([
            'status' => true,
            'message' => 'Pago eliminado correctamente',
        ]);
    }
            public function exportExcel()
    {
        return Excel::download(new PaymentExport, 'Pagos.xlsx');
    }
}
