<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Services\Payment\PaymentDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
            $payments = Payment::with(['customer', 'paymentPlan', 'discount'])
                ->when($customer, function ($query, $customer) {
                    return $query->whereHas('customer', function ($query) use ($customer) {
                        $query->where('name', 'like', "%$customer%");
                    });
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
        try {
            // Prepare validated data
            $validated = $request->validated();

            // Normalize payment_date
            $parsedDate = Carbon::parse($validated['payment_date'])->setTimezone('America/Lima');
            // Si no tiene hora (hora es 00:00:00), usar la hora actual
            if ($parsedDate->format('H:i:s') === '00:00:00') {
                $parsedDate = $parsedDate->setTime(
                    now('America/Lima')->hour,
                    now('America/Lima')->minute,
                    now('America/Lima')->second
                );
            }
            $validated['payment_date'] = $parsedDate->format('Y-m-d H:i:s');

            // Create Payment
            $payment = Payment::create($validated);

            return redirect()->route('panel.payments.index')->with([
                'status' => true,
                'message' => 'Pago creado correctamente',
            ]);
        } catch (\Throwable $th) {
            Log::error('Error storing payment', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'payment_date' => $request->input('payment_date'),
            ]);
            return redirect()->route('panel.payments.index')->with([
                'status' => false,
                'error' => 'Error al registrar el pago: ' . $th->getMessage(),
            ]);
        }
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
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        Gate::authorize('update', $payment);

        try {
            $data = $request->validated();
            $originalFields = ['customer_id', 'payment_plan_id', 'discount_id'];

            foreach ($originalFields as $field) {
                if (!isset($data[$field])) {
                    $data[$field] = $payment->{$field};
                }
            }

            // Normalize payment_date if provided
            if (isset($data['payment_date'])) {
                $parsedDate = Carbon::parse($data['payment_date'])->setTimezone('America/Lima');
                if ($parsedDate->format('H:i:s') === '00:00:00') {
                    $parsedDate = $parsedDate->setTime(
                        now('America/Lima')->hour,
                        now('America/Lima')->minute,
                        now('America/Lima')->second
                    );
                }
                $data['payment_date'] = $parsedDate->format('Y-m-d H:i:s');
            }

            $wasPendingOrVencido = in_array($payment->status, ['pendiente', 'vencido']);
            $isNowPagado = $data['status'] === 'pagado';

            $payment->update($data);

            if (isset($data['service_id']) && $payment->paymentPlan) {
                Log::info('Actualizando service_id', ['service_id' => $data['service_id']]);
                $payment->paymentPlan->update(['service_id' => $data['service_id']]);
            }

            $responseData = [
                'status' => true,
                'message' => 'Pago actualizado correctamente',
                'payment' => new PaymentResource($payment),
            ];

            // Generate document if status changed to 'pagado'
            if ($wasPendingOrVencido && $isNowPagado) {
                try {
                    $documentData = $this->paymentDocumentService->generateDocument($payment);
                    $responseData['document'] = $documentData;
                } catch (\Exception $e) {
                    Log::error('Failed to generate document', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id,
                    ]);
                    return response()->json([
                        'status' => false,
                        'message' => 'Error generating document: ' . $e->getMessage(),
                    ], 500);
                }
            }

            return response()->json($responseData);
        } catch (\Throwable $th) {
            Log::error('Error updating payment', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'payment_date' => $request->input('payment_date'),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el pago: ' . $th->getMessage(),
            ], 500);
        }
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
}
