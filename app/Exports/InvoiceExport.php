<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        return Invoice::with(['payment.customer', 'payment.paymentPlan.service'])
            ->join('payments', 'invoices.payment_id', '=', 'payments.id')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->orderBy('customers.name', 'asc')
            ->select('invoices.*')
            ->get();
    }

    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->payment_id,
            $invoice->document_type === 'B' ? 'Boleta' : 'Factura',
            $invoice->serie_assigned,
            $invoice->correlative_assigned,
            $invoice->payment->customer->name ?? '---',
            $invoice->payment->paymentPlan->service->name ?? '---',
            number_format($invoice->payment->amount, 2),
            optional($invoice->payment->payment_date)->format('d-m-Y') ?? '---',
            ucfirst($invoice->payment->payment_method),
            ucfirst($invoice->sunat ?? 'Pendiente'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'ID Pago',
            'Tipo',
            'Serie',
            'Correlativo',
            'Cliente',
            'Servicio',
            'Monto',
            'Fecha de Pago',
            'Método de Pago',
            'SUNAT',
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(12);   // ID Pago
        $sheet->getColumnDimension('C')->setWidth(12);   // Tipo
        $sheet->getColumnDimension('D')->setWidth(12);   // Serie
        $sheet->getColumnDimension('E')->setWidth(12);   // Correlativo
        $sheet->getColumnDimension('F')->setWidth(25);   // Cliente
        $sheet->getColumnDimension('G')->setWidth(50);   // Servicio
        $sheet->getColumnDimension('H')->setWidth(18);   // Monto
        $sheet->getColumnDimension('I')->setWidth(22);   // Fecha de Pago
        $sheet->getColumnDimension('J')->setWidth(22);   // Método
        $sheet->getColumnDimension('K')->setWidth(20);   // SUNAT 


        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:K' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $sheet->getStyle('H2:H' . $highestRow)->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        return [];
    }
}
