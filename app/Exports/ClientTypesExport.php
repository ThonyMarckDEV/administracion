<?php

namespace App\Exports;

use App\Models\ClientType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientTypesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ClientType::orderBy('id', 'asc')->get();
    }

    public function map($clientType): array
    {
        return [
            $clientType->id,
            $clientType->name,
            $clientType->created_at->format('d-m-Y H:i:s'),
            $clientType->state == 1 ? 'Activo' : 'Inactivo',
        ];
    }

    public function headings():array{
        return[
            'ID',
            'Tipo de cliente',
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
        $sheet->getColumnDimension('C')->setWidth(30);  // Nombre
        $sheet->getColumnDimension('D')->setWidth(12);  // Costo

        $sheet->getStyle('A1:C1')->applyFromArray([
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

        $sheet->getStyle('A2:C' . ($sheet->getHighestRow()))->applyFromArray([

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