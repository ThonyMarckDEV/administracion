<?php

namespace App\Exports;

use App\Models\Period;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeriodsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Period::orderBy('id', 'asc')->get();
    }

    public function map($period): array
    {
        return [
            $period->id,
            $period->name,
            $period->description,
            $period->state == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function headings():array{
        return[
            'ID',
            'Periodo',
            'Descripción',
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
        $sheet->getColumnDimension('C')->setWidth(30);  // Costo
        $sheet->getColumnDimension('D')->setWidth(30);  // Fecha
        
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],  
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        $sheet->getStyle('A2:D' . ($sheet->getHighestRow()))->applyFromArray([

            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                ],
            ],
        ]);

        return [];
    }
}
