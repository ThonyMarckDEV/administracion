<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
    return Payment::with(['customer', 'paymentPlan.service', 'paymentPlan.period'])
        ->join('customers', 'payments.customer_id', '=', 'customers.id') 
        ->orderBy('customers.name', 'asc')                               
        ->select('payments.*')                                           
        ->get();
        }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->customer->name ?? '---',
            $payment->paymentPlan->service->name ?? '---',
            $payment->paymentPlan->period->name ?? '---',
            $payment->amount,
            optional($payment->payment_date)->format('d-m-Y') ?? '---',
            ucfirst($payment->payment_method),
            $payment->reference ? "--" . strtoupper($payment->reference) . "--" : '---',
            ucfirst($payment->status),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Servicio',
            'Periodo',
            'Monto',
            'Fecha de Pago',
            'Método de Pago',
            'Referencia',
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
        $sheet->getColumnDimension('B')->setWidth(30);  // Cliente
        $sheet->getColumnDimension('C')->setWidth(40);  // Servicio
        $sheet->getColumnDimension('D')->setWidth(18);  // Periodo
        $sheet->getColumnDimension('E')->setWidth(12);  // Monto
        $sheet->getColumnDimension('F')->setWidth(18);  // Fecha de pago
        $sheet->getColumnDimension('G')->setWidth(18);  // Método
        $sheet->getColumnDimension('H')->setWidth(25);  // Referencia
        $sheet->getColumnDimension('I')->setWidth(15);  // Estado

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A2:I' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        return [];
    }
}
