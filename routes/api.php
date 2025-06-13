<?php


use App\Http\Controllers\Sunat\ComprobanteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/factura', [ComprobanteController::class, 'createFactura'])->name('factura');
Route::post('/boleta', [ComprobanteController::class, 'createBoleta'])->name('boleta');
Route::post('/void', [ComprobanteController::class, 'voidComprobante']);