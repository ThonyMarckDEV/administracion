<?php

namespace App\Http\Controllers\Sunat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBoletaRequest;
use App\Http\Requests\StoreFacturaRequest;
use App\Services\Sunat\ComprobanteService;
use Illuminate\Support\Facades\Log;

class FacturationController extends Controller
{
    protected $comprobanteService;

    public function __construct(ComprobanteService $comprobanteService)
    {
        $this->comprobanteService = $comprobanteService;
    }

    public function createFactura(StoreFacturaRequest $request)
    {
        try {
            $validated = $request->validated();
            $invoice = $this->comprobanteService->createFactura($validated);
            $result = $this->comprobanteService->sendComprobante($invoice, $validated['id_pago']);

            return response()->json([
                'message' => 'Invoice processed successfully',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing invoice: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error processing invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createBoleta(StoreBoletaRequest $request)
    {
        try {
            $validated = $request->validated();
            $boleta = $this->comprobanteService->createBoleta($validated);
            $result = $this->comprobanteService->sendComprobante($boleta, $validated['id_pago']);

            return response()->json([
                'message' => 'Receipt processed successfully',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing receipt: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error processing receipt',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}