<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmountRequest;
use App\Http\Requests\UpdateAmountRequest;
use App\Http\Resources\AmountResource;
use App\Http\Resources\AmountShowResource;
use App\Models\Amount;
use App\Models\Category;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AmountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Amount::class);
        return Inertia::render('panel/amount/indexAmount');
    }

    public function listAmount(Request $request)
    {
        Gate::authorize('viewAny', Amount::class);
    
        try {
            $ruc = $request->get('ruc');
            $date_start = $request->get('date_start');
            $date_end = $request->get('date_end');
    
            $amounts = Amount::when($ruc, function ($query, $ruc) {
                    return $query->whereHas('suppliers', function ($query) use ($ruc) {
                        $query->where('ruc', 'like', "$ruc%");
                    });
                })
                ->when($date_start && $date_end, function ($query) use ($date_start, $date_end) {
                    $start = Carbon::parse($date_start)->startOfDay();
                    $end = Carbon::parse($date_end)->endOfDay();
                    return $query->whereBetween('date_init', [$start, $end]);
                })
                ->orderBy('id', 'asc')
                ->paginate(12);
    
            return response()->json([
                'amounts' => AmountResource::collection($amounts),
                'pagination' => [
                    'total' => $amounts->total(),
                    'current_page' => $amounts->currentPage(),
                    'per_page' => $amounts->perPage(),
                    'last_page' => $amounts->lastPage(),
                    'from' => $amounts->firstItem(),
                    'to' => $amounts->lastItem()
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los egresos',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('panel/amount/components/formAmount');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAmountRequest $request)
    {
        Gate::authorize('create', Amount::class);
        $validated = Amount::create($request->validated());
        return redirect()->route('panel.amounts.index')->with('message','Egreso registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Amount $amount)
    {
        Gate::authorize('view', $amount);
        return response()->json([
            'status' => true,
            'message' => 'Egreso encontrado',
            'amount' => new AmountShowResource($amount)
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAmountRequest $request, Supplier $supplier)
    {
        Gate::authorize('update', $supplier);
        $validated = $request->validated();
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $supplier->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'egreso actualizado correctamente',
            'amount' => new AmountResource($supplier)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amount $amount)
    {
        Gate::authorize('delete', $amount);
        $amount->delete();
        return response()->json([
            'status' => true,
            'message' => 'egreso eliminado correctamente',
        ]);
    }
}
