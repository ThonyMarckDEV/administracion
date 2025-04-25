<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Http\Requests\StorePaymentPlanRequest;
use App\Http\Requests\UpdatePaymentPlanRequest;
use App\Http\Resources\PaymentPlanResource;
use App\Models\Period;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PaymentPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', PaymentPlan::class);
        return Inertia::render('panel/paymentPlan/indexPaymentPlan');
    }

    public function listarPaymentPlans(Request $request)
    {
        Gate::authorize('viewAny', PaymentPlan::class);
        try {
            $name = $request->get('name');
            $paymentPlans = PaymentPlan::with('service', 'period')->when($name, function ($query, $name) {
                return $query->whereHas('service', function ($q) use ($name) {
                    $q->where('name', 'LIKE', "%$name%");
                });
            })->orderBy('id', 'asc')->orderBy('service_id', 'asc')->paginate(12);
            return response()->json([
                'paymentPlans' => PaymentPlanResource::collection($paymentPlans),
                'pagination' => [
                    'total' => $paymentPlans->total(),
                    'current_page' => $paymentPlans->currentPage(),
                    'per_page' => $paymentPlans->perPage(),
                    'last_page' => $paymentPlans->lastPage(),
                    'from' => $paymentPlans->firstItem(),
                    'to' => $paymentPlans->lastItem(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los planes de pago',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::select('id', 'name')
            ->where('state', 'activo')
            ->orderBy('id')
            ->get();

        $periods = Period::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();

        return Inertia::render('panel/paymentPlan/components/formPaymentPlan', [
            'paymentPlanService' => $services,
            'paymentPlanPeriod' => $periods
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentPlanRequest $request)
    {
        Gate::authorize('create', PaymentPlan::class);

        $validated = $request->validated();

        // ✅ Convertir ANTES de guardar en la DB
        $validated['payment_type'] = $validated['payment_type'] === 'anual';

        $paymentPlan = PaymentPlan::create($validated);

        return redirect()
            ->route('panel.paymentPlans.index')
            ->with('message', 'Plan de pago "' . $paymentPlan->id . '" creado correctamente');
    }



    /**
     * Display the specified resource.
     */
    public function show(PaymentPlan $paymentPlan)
    {
        Gate::authorize('view', $paymentPlan);

        return response()->json([
            'status' => true,
            'message' => 'Plan de pago encontrado',
            'paymentPlan' => new PaymentPlanResource($paymentPlan)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentPlanRequest $request, PaymentPlan $paymentPlan)
    {
        Gate::authorize('update', $paymentPlan);

        $validated = $request->validated();

        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $validated['payment_type'] = ($validated['payment_type'] ?? 'mensual') === 'anual';

        $paymentPlan->update($validated);

        return response()->json([
            'state' => true,
            'message' => 'Plan de pago actualizado correctamente',
            'paymentPlan' => new PaymentPlanResource($paymentPlan)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentPlan $paymentPlan)
    {
        Gate::authorize('delete', $paymentPlan);
        $paymentPlan->delete();
        return response()->json([
            'status' => true,
            'message' => 'Plan de pago eliminado correctamente'
        ]);
    }
}
