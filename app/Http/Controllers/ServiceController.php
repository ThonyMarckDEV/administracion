<?php
namespace App\Http\Controllers;

use App\Exports\ServicesExport;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Imports\ServiceImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        Gate::authorize('viewAny', Service::class);
        return Inertia::render('panel/service/indexService');
    }

    public function listarServices(Request $request)
    {
        Gate::authorize('viewAny', Service::class);
        try {
            $name = $request->get('name');
            $services = Service::when($name, function ($query, $name) {
                return $query->where('name', 'like', "%$name%");
            })->orderBy('id','asc')->paginate(12);

            return response()->json([
                'services' => ServiceResource::collection($services),
                'pagination' => [
                    'total' => $services->total(),
                    'current_page' => $services->currentPage(),
                    'per_page' => $services->perPage(),
                    'last_page' => $services->lastPage(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem()
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los servicios',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        Gate::authorize('create', Service::class);
        return Inertia::render('panel/service/components/formService');
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request)
    {
        Gate::authorize('create', Service::class);
        try {
            $validatedData = $request->validated();
            $service = Service::create($validatedData);
            return response()->json(new ServiceResource($service), 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al crear el servicio',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        Gate::authorize('view', $service);
        return response()->json(new ServiceResource($service));
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        Gate::authorize('update', $service);
        try {
            $validatedData = $request->validated();
            $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
            $service->update($validatedData);
            return response()->json(new ServiceResource($service));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al actualizar el servicio',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);
        try {
            $service->delete();
            return response()->json(null, 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al eliminar el servicio',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // EXPORTAR A EXCEL
    public function exportExcel()
    {
        return Excel::download(new ServicesExport, 'servicios.xlsx');
    }

    // IMPORTAR EXCEL
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new ServiceImport, $request->file('archivo'));
        return response()->json([
            'message' => 'Servicios importados de manera correcta',
        ]);
    }
}