<?php

use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ServiceController;
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
    });
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
