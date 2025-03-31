<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DiscountController extends Controller
{

    // function to return the view of the proveedor module

    public function index()
    {
        Gate::authorize('viewAny', Discount::class);
        return Inertia::render('panel/discount/indexDiscount');
    }

    public function listarDiscounts(Request $request)
    {
        // authorization so you can access the method

        Gate::authorize('viewAny', Discount::class);

        try {
            $description = $request->get('description');
            $discounts = Discount::when($description, function ($query, $description) {
                return $query->whereLike('description', "%$description%");
            })->orderBy('id','asc')->paginate(15);
            return response()->json([
                'discounts'=> DiscountResource::collection($discounts),
                'pagination' => [
                    'total' => $discounts->total(),
                    'current_page' => $discounts->currentPage(),
                    'per_page' => $discounts->perPage(),
                    'last_page' => $discounts->lastPage(),
                    'from' => $discounts->firstItem(),
                    'to' => $discounts->lastItem(),
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al listar los descuentos',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        return Inertia::render('panel/discount/components/formDiscount');
    }
    public function store(StoreDiscountRequest $request)
     {
         Gate::authorize('create', Discount::class);
         $validated = $request->validated(); // ya tiene state como boolean
         $discount = Discount::create($validated);     
         return redirect()->route('panel.discounts.index')->with('message', 'Descuento creado correctamente'); 
     }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        Gate::authorize('view', $discount);
        return response()->json([
            'state' => true,
            'message' => 'Descuento encontrado',
            'discount' => new DiscountResource($discount),
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiscountRequest $request, Discount $discount)
    {
         Gate::authorize('update', $discount);
         $validated = $request->validated();
         $validated['state'] = ($validated['state'] ?? false) === true;
         $discount->update($validated);
         return response()->json([
             'state' => true,
             'message' => 'Descuento actualizado de manera correcta',
             'discount' => new DiscountResource($discount->refresh()),
         ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        Gate::authorize('delete', $discount);
        $discount->delete();
        return response()->json([
            'state' => true,
            'message' => 'Descuento eliminado de manera correcta',
        ]);
    }
}
