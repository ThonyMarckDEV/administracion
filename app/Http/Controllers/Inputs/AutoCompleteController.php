<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AutoCompleteController extends Controller
{
    public function getSuppliersList(Request $request)
    {
        $ruc = $request->get('texto');

        $suppliers = Supplier::select('id', 'name', 'ruc')
            ->where('state', true)
            ->when($ruc, function ($query, $ruc) {
                return $query->whereLike('name', "%$ruc%");
            })
            ->orderBy('id')
            ->limit(5)
            ->get();
        return response()->json($suppliers);
    }

    public function getCustomerList(Request $request)
    {
        $name = $request->get('texto');

        $customers = Customer::select('id', 'name')
            ->where('state', true)
            ->when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })
            ->orderBy('id')
            ->limit(5)
            ->get();
        return response()->json($customers);
    }

    public function getServiceList(Request $request)
    {
        $name = $request->get('texto');

        $services = Service::select('id', 'name')
            ->whereLike('state', "activo")
            ->when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })
            ->orderBy('id')
            ->limit(5)
            ->get();
        return response()->json($services);
    }
}
