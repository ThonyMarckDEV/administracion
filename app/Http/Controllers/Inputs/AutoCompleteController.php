<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AutoCompleteController extends Controller
{
    public function getSuppliersList(Request $request){
        $ruc = $request->get('texto');

        $suppliers = Supplier::select('id','name','ruc')
        ->where('state',true)
        ->when($ruc, function ($query, $ruc) {
            return $query->whereLike('name', "%$ruc%");
        })
        ->orderBy('id')
        ->limit(5)
        ->get();
        return response()->json($suppliers);
    }
}
