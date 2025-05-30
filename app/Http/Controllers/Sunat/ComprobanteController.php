<?php

namespace App\Http\Controllers\Sunat;

use App\Services\Sunat\GenerateComprobante;
use App\Http\Requests\StoreFacturaRequest;
use App\Http\Requests\StoreBoletaRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ComprobanteController extends Controller
{
    protected $comprobanteService;

    public function __construct(GenerateComprobante $comprobanteService)
    {
        $this->comprobanteService = $comprobanteService;
    }

    public function createFactura(StoreFacturaRequest $request)
    {
        $start = microtime(true);
        try {
            $validated = $request->validated();
            $invoice = $this->comprobanteService->createComprobante($validated, 'factura');
            $result = $this->comprobanteService->sendComprobante($invoice, $validated['id_pago']);

            Log::info('Factura processed', [
                'payment_id' => $validated['id_pago'],
                'execution_time' => microtime(true) - $start,
            ]);

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
        $start = microtime(true);
        try {
            $validated = $request->validated();
            $boleta = $this->comprobanteService->createComprobante($validated, 'boleta');
            $result = $this->comprobanteService->sendComprobante($boleta, $validated['id_pago']);
            
            Log::info('Boleta processed', [
                'payment_id' => $validated['id_pago'],
                'execution_time' => microtime(true) - $start,
            ]);

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