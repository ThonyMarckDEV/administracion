<?php

namespace App\Http\Controllers\Inputs;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ClientType;
use App\Models\Discount;
use App\Models\Period;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    // get client_type list
    public function getClientTypeList()
    {
        $clientTypes = ClientType::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return response()->json($clientTypes);
    }

    // get category list
    public function getCategoriesList(Request $request)
    {
        $name = $request->get('texto');
        $categories = Category::select('id', 'name')
            ->where('status', true)
            ->when($name, function ($query, $name) {
                return $query->whereLike('name', "%$name%");
            })
            ->orderBy('id')
            ->limit(5)
            ->get();
        return response()->json($categories);
    }

    // get service list
    public function getServiceList()
    {
        $services = Service::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return response()->json($services);
    }

    // get period list
    public function getPeriodList()
    {
        $periods = Period::select('id', 'name')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return response()->json($periods);
    }

        // get customer list
    public function getCustomerList()
    {
        $customers = Customer::select('id', 'name')
            ->orderBy('id')
            ->get();
        return response()->json($customers);
    }

    public function getDiscountList()
    {
        $discounts = Discount::select('id', 'description', 'percentage')
            ->where('state', 1)
            ->orderBy('id')
            ->get();
        return response()->json($discounts);
    }
}
