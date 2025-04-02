<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       Gate::authorize('viewAny', Customer::class);
        return Inertia::render('panel/customer/indexCustomer');
    }

    public function listarCustomers(Request $request){
        Gate::authorize('viewAny', Customer::class);
        try {
            $name = $request->get('name');
            $customers = Customer::when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })->orderBy('id','asc')->paginate(10);
            return response()->json([
                'customers' => CustomerResource::collection($customers),
                'pagination' => [
                    'total' => $customers->total(),
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'last_page' => $customers->lastPage(),
                    'from' => $customers->firstItem(),
                    'to' => $customers->lastItem()
                ]
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los clientes',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('panel/customer/components/formCustomer');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        Gate::authorize('create', Customer::class); 
        $validated = $request->validated();
        $customer = Customer::create($validated);
        return redirect()->route('panel.customers.index')->with('message', 'Cliente creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        Gate::authorize('view', $customer);
        return response()->json([
            'status' => true,
            'message' => 'Cliente encontrado',
            'customer' => new CustomerResource($customer)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        Gate::authorize('update', $customer);
        $validated = $request->validated();
        $validated['state'] = ($validated['state'] ?? 'inactivo') === 'activo';
        $customer->update($validated);
        return response()->json([
            'status' => true,  
            'message' => 'Cliente actualizado correctamente',
            'customer' => new CustomerResource($customer)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        Gate::authorize('delete', $customer);
        $customer->delete();
        return response()->json([
            'status' => true,
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
}
