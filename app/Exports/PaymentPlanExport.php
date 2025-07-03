<?php

namespace App\Exports;

use App\Models\PaymentPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentPlanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        return PaymentPlan::with(['service', 'customer', 'period'])
            ->join('services', 'payment_plans.service_id', '=', 'services.id') 
            ->orderBy('services.name', 'asc')                                  
            ->select('payment_plans.*')                                        
            ->get();    }

    public function map($plan): array
    {
        return [
            $plan->id,
            $plan->service->name ?? '---',
            $plan->customer->name ?? '---',
            $plan->period->name ?? '---',
            $plan->payment_type ? 'Anual' : 'Mensual',
            number_format($plan->amount, 2),
            $plan->duration,
            $plan->state ? 'Activo' : 'Inactivo',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Servicio',
            'Cliente',
            'Periodo',
            'Tipo de Pago',
            'Monto',
            'Duración',
            'Estado',
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(40);  // Servicio
        $sheet->getColumnDimension('C')->setWidth(30);  // Cliente
        $sheet->getColumnDimension('D')->setWidth(18);  // Periodo
        $sheet->getColumnDimension('E')->setWidth(15);  // Tipo
        $sheet->getColumnDimension('F')->setWidth(12);  // Monto
        $sheet->getColumnDimension('G')->setWidth(10);  // Duración
        $sheet->getColumnDimension('H')->setWidth(15);  // Estado

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A2:H' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $sheet->getStyle('F2:F' . $highestRow)->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        return [];
    }
}