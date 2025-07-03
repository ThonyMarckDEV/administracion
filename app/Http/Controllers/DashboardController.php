<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
public function index()
{
    // Generar meses del año 2025
    $meses = collect(range(1, 12))->map(function ($mes) {
        return Carbon::create(2025, $mes, 1)->format('Y-m');
    });

    // Pagados por mes
    $pagados = Payment::where('status', 'pagado')
        ->whereYear('payment_date', 2025)
        ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as mes, COUNT(*) as total")
        ->groupBy('mes')
        ->pluck('total', 'mes');

    // Vencidos por mes
    $noPagados = Payment::where('status', 'vencido')
        ->whereYear('payment_date', 2025)
        ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as mes, COUNT(*) as total")
        ->groupBy('mes')
        ->pluck('total', 'mes');
        // Vencidos por mes
    $pendientes = Payment::where('status', 'pendiente')
        ->whereYear('payment_date', 2025)
        ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as mes, COUNT(*) as total")
        ->groupBy('mes')
        ->pluck('total', 'mes');

    // Montos pagados por año
$pagosPorAño = Payment::where('status', 'pagado')
    ->selectRaw('EXTRACT(YEAR FROM payment_date) as anio, SUM(amount) as total')
    ->groupBy('anio')
    ->orderBy('anio')
    ->get();

$labelsAño = $pagosPorAño->pluck('anio')->map(fn($a) => (string) $a)->toArray();
$montosAño = $pagosPorAño->pluck('total')->map(fn($m) => (float) $m)->toArray();

// Monto total pagado por mes en 2025
    $montoPagadoPorMes = Payment::where('status', 'pagado')
        ->whereYear('payment_date', 2025)
        ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as mes, SUM(amount) as total")
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();


        // Clientes que más han pagado por servicios
   $clientesChart = Payment::where('status', 'pagado')
    ->whereYear('payment_date', 2025)
    ->join('customers', 'payments.customer_id', '=', 'customers.id')
    ->selectRaw('customers.name as nombre, SUM(amount) as total_pagado')
    ->groupBy('customers.name')
    ->orderByDesc('total_pagado')
    ->limit(10)
    ->get();

$labelsClientes = $clientesChart->pluck('nombre');
$valuesClientes = $clientesChart->pluck('total_pagado')->map(fn($v) => (float) $v);



// Convertir a arrays simples
$montoPagadoChart = [
    'labels' => $montoPagadoPorMes->pluck('mes')->toArray(),
    'values' => $montoPagadoPorMes->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
];

    // Etiquetas en español tipo Ene, Feb, etc.
    $labels = $meses->map(function ($mes) {
        return Carbon::createFromFormat('Y-m', $mes)->locale('es')->translatedFormat('M');
    });

    $valoresPagados = $meses->map(fn($m) => $pagados[$m] ?? 0);
    $valoresNoPagados = $meses->map(fn($m) => $noPagados[$m] ?? 0);
    $valoresPendientes = $meses->map(fn($m) => $pendientes[$m] ?? 0);


    return Inertia::render('Dashboard', [
        'labels' => $labels,
        'pagados' => $valoresPagados,
        'noPagados' => $valoresNoPagados,
        'pendientes' => $valoresPendientes,
    'labelsMontos' => $montoPagadoChart['labels'],
    'valoresMontos' => $montoPagadoChart['values'],
    'labelsPorAnio' => $labelsAño,
    'montosPorAnio' => $montosAño,
        'clientesChart' => [
        'labels' => $labelsClientes,
        'values' => $valuesClientes,
    ],
    ]);
}
}