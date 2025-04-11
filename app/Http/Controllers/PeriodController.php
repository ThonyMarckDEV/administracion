<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Http\Requests\StorePeriodRequest;
use App\Http\Requests\UpdatePeriodRequest;
use App\Http\Resources\PeriodResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Period::class);
        return Inertia::render('panel/period/indexPeriod');
    }

    public function listarPeriods(Request $request)
    {
        Gate::authorize('viewAny', Period::class);
        try {
            $name = $request->get('name');
            $periods = Period::when($name, function ($query, $name) {
                return $query->where('name', 'like', "%$name%");
            })->orderBy('id','asc')->paginate(15);

            return response()->json([
                'periods' => PeriodResource::collection($periods),
                'pagination' => [
                    'total' => $periods->total(),
                    'current_page' => $periods->currentPage(),
                    'per_page' => $periods->perPage(),
                    'last_page' => $periods->lastPage(),
                    'from' => $periods->firstItem(),
                    'to' => $periods->lastItem()
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los periodos',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('panel/period/components/formPeriod');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePeriodRequest $request)
    {
        Gate::authorize('create', Period::class);
        $validated = $request->validated();
        $validated = $request->safe()->except(['state']);
        $clientType = Period::create(Arr::except($validated, ['state']));
        return redirect()->route('panel.periods.index')->with('message', 'Periodo creado correctamente'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Period $period)
    {
        Gate::authorize('view', $period);
        return response()->json([
            'state' => true,
            'message' => 'Periodo encontrado',
            'period' => new PeriodResource($period),
        ], 200);
    }

    public function update(UpdatePeriodRequest $request, Period $period)
    {
        Gate::authorize('update', $period);
        $validated = $request->validated();
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $period->update($validated);
        return response()->json([
            'state' => true,
            'message' => 'Periodo actualizado de manera correcta',
            'period' => new PeriodResource($period->refresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Period $period)
    {
        Gate::authorize('delete', $period);
        $period->delete();
        return response()->json([
            'state' => true,
            'message' => 'Periodo eliminado de manera correcta',
        ]);
    }
}
