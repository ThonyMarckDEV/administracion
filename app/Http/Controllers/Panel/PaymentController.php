<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
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
        $payment->update($data);
        if (($data['service_id'] ?? null) !== null && $payment->paymentPlan) {
            Log::info('Actualizando service_id', ['service_id' => $data['service_id']]);
            $payment->paymentPlan->update(['service_id' => $data['service_id']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pago actualizado correctamente',
            'payment' => new PaymentResource($payment),
        ]);
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
