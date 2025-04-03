<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Models\ClientType;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    // get client_type list
    public function getClientTypeList(){
        $clientTypes = ClientType::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return response()->json($clientTypes);
    }
}
