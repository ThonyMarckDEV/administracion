<?php

namespace App\Http\Controllers\Panel;

use App\Exports\AmountsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmountRequest;
use App\Http\Requests\UpdateAmountRequest;
use App\Http\Resources\AmountResource;
use App\Http\Resources\AmountShowResource;
use App\Models\Amount;
use App\Imports\AmountImport;
use App\Models\SeriesCorrelative;
use App\Services\Sunat\GenerateReciboHonorariosPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
            Log::error('Error listing amounts', ['error' => $th->getMessage()]);
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
        try {
            // Fetch existing series_correlative for RHE
            $seriesCorrelative = SeriesCorrelative::where('document_type', 'RHE')
                ->where('serie', '001')
                ->first();

            if (!$seriesCorrelative) {
                throw new \Exception('No series_correlative found for RHE with serie 001');
            }

            // Get the next correlative and increment
            $correlative = $seriesCorrelative->correlative + 1;
            $seriesCorrelative->update(['correlative' => $correlative]);

            // Prepare validated data
            $validated = $request->validated();

            // Normalize date_init
            $parsedDate = Carbon::parse($validated['date_init'])->setTimezone('America/Lima');
            // Si no tiene hora (hora es 00:00:00), usar la hora actual
            if ($parsedDate->format('H:i:s') === '00:00:00') {
                $parsedDate = $parsedDate->setTime(
                    now('America/Lima')->hour,
                    now('America/Lima')->minute,
                    now('America/Lima')->second
                );
            }
            $validated['date_init'] = $parsedDate->format('Y-m-d H:i:s');

            // Assign series and correlative
            $validated['serie_assigned'] = $seriesCorrelative->serie;
            $validated['correlative_assigned'] = $correlative;

            // Create Amount
            $amount = Amount::create($validated);

            return redirect()->route('panel.amounts.index')->with('message', 'Egreso registrado correctamente');
        } catch (\Throwable $th) {
            Log::error('Error storing amount', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'date_init' => $request->input('date_init'),
            ]);
            return redirect()->route('panel.amounts.index')->with('error', 'Error al registrar el egreso: ' . $th->getMessage());
        }
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
    public function update(UpdateAmountRequest $request, Amount $amount)
    {
        Gate::authorize('update', $amount);
        $validated = $request->validated();
        $amount->update($validated);
        return response()->json([
            'status' => true,
            'message' => 'egreso actualizado correctamente',
            'amount' => new AmountResource($amount)
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

    /**
     * Generate PDF for a specific amount.
     */
    public function generatePdf(Amount $amount)
    {
        Gate::authorize('view', $amount);

        try {
            Log::info('Generating PDF for amount', ['id' => $amount->id]);
            $generator = new GenerateReciboHonorariosPdf();
            $pdfContent = $generator->generate([
                'razon_social' => $amount->suppliers->name,
                'ruc' => $amount->suppliers->ruc,
                'service' => $amount->description,
                'monto' => $amount->amount,
                'retention' => $amount->amount * 0.08,
                'monto_neto' => $amount->amount * (1 - 0.08),
                'fecha_emision' => Carbon::parse($amount->date_init)->format('d/m/Y'),
                'hora_emision' => Carbon::parse($amount->date_init)->format('H:i:s'),
                'doc_series' => ' RHE ' . $amount->serie_assigned,
                'doc_correlative' => $amount->correlative_assigned,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'PDF generado correctamente',
                'pdf' => base64_encode($pdfContent),
            ]);
        } catch (\Throwable $th) {
            Log::error('Error generating PDF in AmountController', [
                'amount_id' => $amount->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Error al generar el PDF',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // EXPORTAR A EXCEL
    public function exportExcel()
    {
        return Excel::download(new AmountsExport, 'Egresos.xlsx');
    }

    // IMPORTAR EXCEL
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new AmountImport, $request->file('archivo'));
        return response()->json([
            'message' => 'Egresos importados de manera correcta',
        ]);
    }
}
