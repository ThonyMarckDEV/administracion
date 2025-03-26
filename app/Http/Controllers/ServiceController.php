<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     */
    public function index()
    {
        return response()->json(Service::all());
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());
        return response()->json($service, 201);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        return response()->json($service);
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
    }
}
