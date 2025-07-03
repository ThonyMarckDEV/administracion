<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::orderBy('name', 'asc')->get();
    }

    public function map($customer):array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->codigo,
            $customer->clienteType->name,
            $customer->created_at->format('d-m-Y H:i:s'),
            $customer->state == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function headings():array{
        return[
            'ID',
            'Nombre',
            'Codigo',
            'Tipos de cliente',
            'Fecha',
            'Estado'
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(8);   // ID
        $sheet->getColumnDimension('B')->setWidth(30);  // Nombre
        $sheet->getColumnDimension('C')->setWidth(15);  // Codigo
        $sheet->getColumnDimension('D')->setWidth(15);  // Tipo de cliente
        $sheet->getColumnDimension('E')->setWidth(25);  // Fecha
        $sheet->getColumnDimension('F')->setWidth(10);  // Estado
        
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A2:F' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        return [];
    }
}
