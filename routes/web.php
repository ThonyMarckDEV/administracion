<?php

use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\Reportes\ClientTypePDFController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Reportes\ServicePDFController;
use App\Http\Controllers\Reportes\SupplierPDFController;
use App\Http\Controllers\Reportes\UserPDFController;
use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



# route group for panel
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('panel')->name('panel.')->group(function () {
        # module users
            Route::resource('users', UserController::class);
        # list users
            Route::get('listar-users',[UserController::class,'listarUsers'])->name('users.listar');
        # module suppliers
            Route::resource('suppliers', SupplierController::class);
        # list suppliers
            Route::get('listar-suppliers',[SupplierController::class,'listarProveedor'])->name('suppliers.listar');    
        # module Services
            Route::resource('services', ServiceController::class);
        # list Services
            Route::get('listar-services',[ServiceController::class,'listarServices'])->name('services.listar');
        # module Client Types
            Route::resource('clientTypes', ClientTypeController::class);
        # list Client Types
            Route::get('listar-clientTypes',[ClientTypeController::class,'listarClientTypes'])->name('clientTypes.listar');
        # module Discount
            Route::resource('discounts', DiscountController::class);
        # list Discount
            Route::get('listar-discounts',[DiscountController::class,'listarDiscounts'])->name('discounts.listar');
      
      
        # Route group for reports
        Route::prefix('reports')->name('reports.')->group(function () {
            # Exports to Excel
            Route::get('/export-excel-users',[UserController::class,'exportExcel'])->name('users.excel');
            Route::get('/export-excel-suppliers',[SupplierController::class,'exportExcel'])->name('suppliers.excel');
            Route::get('/export-excel-services',[ServiceController::class,'exportExcel'])->name('services.excel');
            Route::get('/export-excel-clientTypes',[ClientTypeController::class,'exportExcel'])->name('clientTypes.excel');

            # Exports to PDF con TCPDF
            Route::get('/export-pdf-users', [UserPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-suppliers', [SupplierPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-services', [ServicePDFController::class, 'exportPDF']);
            Route::get('/export-pdf-clientTypes', [ClientTypePDFController::class, 'exportPDF']);
        });
    });
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
