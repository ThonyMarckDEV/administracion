<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Service::orderBy('id', 'asc')->get();
    }

    public function map($service): array
    {
        return [
            $service->id,
            $service->name,
            $service->cost,
            $service->ini_date,
            $service->state === 'activo' ? 'Activo' : ($service->state === 'pendiente' ? 'Pendiente' : 'Inactivo'),
        ];
    }

    public function headings():array{
        return[
            'ID',
            'Nombre',
            'Costo',
            'Fecha de Inicio',
            'Estado'
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
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

        $sheet->getStyle('A2:E' . ($sheet->getHighestRow()))->applyFromArray([

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

        // Estilo específico para la columna de 'Costo' (columna C)
        $sheet->getStyle('C2:C' . ($sheet->getHighestRow()))->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

        return [];
    }
}