<?php


use App\Http\Controllers\Sunat\FacturationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/factura', [FacturationController::class, 'createFactura']);

Route::post('/boleta', [FacturationController::class, 'createBoleta']);