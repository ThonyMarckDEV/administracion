
<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\Reportes\ClientTypePDFController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Reportes\ServicePDFController;
use App\Http\Controllers\Reportes\SupplierPDFController;
use App\Http\Controllers\Reportes\UserPDFController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\Inputs\AutoCompleteController;
use App\Http\Controllers\Inputs\SelectController;
use App\Http\Controllers\Panel\AmountController;
use App\Http\Controllers\Reportes\CategoryPDFController;
use App\Http\Controllers\Panel\CustomerController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\Reportes\CustomerPDFController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

# list prueba suppliers 

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
        # module Categories
        Route::resource('categories', CategoryController::class);
        # list Categories
        Route::get('listar-categories',[CategoryController::class,'listarCategories'])->name('categories.listar');
        # module Customers
        Route::resource('customers', CustomerController::class); 
        # list Customers
         Route::get('listar-customers',[CustomerController::class,'listarCustomers'])->name('customers.listar');
        # module Periods
        Route::resource('periods', PeriodController::class);
        # list Periods
        Route::get('listar-periods',[PeriodController::class,'listarPeriods'])->name('periods.listar');
        # module Amount
        Route::resource('amounts', AmountController::class);
        # list Amount
        Route::get('listar-amounts',[AmountController::class,'listAmount'])->name('amounts.listar');

        # Route group for reports
        Route::prefix('reports')->name('reports.')->group(function () {
            # Exports to Excel
            Route::get('/export-excel-users',[UserController::class,'exportExcel'])->name('users.excel');
            Route::get('/export-excel-suppliers',[SupplierController::class,'exportExcel'])->name('suppliers.excel');
            Route::get('/export-excel-services',[ServiceController::class,'exportExcel'])->name('services.excel');
            Route::get('/export-excel-clientTypes',[ClientTypeController::class,'exportExcel'])->name('clientTypes.excel');
            Route::get('/export-excel-categories',[CategoryController::class,'exportExcel'])->name('categories.excel');
            Route::get('/export-excel-customers',[CustomerController::class,'exportExcel'])->name('customers.excel');
            Route::get('/export-excel-periods',[PeriodController::class,'exportExcel'])->name('periods.excel');

            # Exports to PDF
            Route::get('/export-pdf-users', [UserPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-suppliers', [SupplierPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-services', [ServicePDFController::class, 'exportPDF']);
            Route::get('/export-pdf-clientTypes', [ClientTypePDFController::class, 'exportPDF']);
            Route::get('/export-pdf-categories', [CategoryPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-customers', [CustomerPDFController::class, 'exportPDF']);
            Route::get('/export-pdf-periods', [PeriodController::class, 'exportPDF']);

            #Excel imports
            Route::post('/import-excel-clientTypes', [ClientTypeController::class, 'importExcel'])->name('reports.clientTypes.import');
            Route::post('/import-excel-customers', [CustomerController::class, 'importExcel'])->name('reports.customers.import');
            Route::post('/import-excel-users', [UserController::class, 'importExcel'])->name('reports.users.import');
            Route::post('/import-excel-suppliers', [SupplierController::class, 'importExcel'])->name('reports.suppliers.import');
            Route::post('/import-excel-services', [ServiceController::class, 'importExcel'])->name('reports.services.import');
            Route::post('/import-excel-periods', [PeriodController::class, 'importExcel'])->name('reports.periods.import');
            Route::post('/import-excel-categories', [CategoryController::class, 'importExcel'])->name('reports.categories.import');
        });

        # Route group for inputs, selects and autocomplete
        Route::prefix('inputs')->name('inputs.')->group(function(){
            # get client_type list
            Route::get('client_type_list',[SelectController::class,'getClientTypeList'])->name('client_type_list');
            Route::get('categories_list',[SelectController::class,'getCategoriesList'])->name('categories_list');
            Route::get('suppliers_list',[AutoCompleteController::class,'getSuppliersList'])->name('suppliers_list');
        });
    });
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
