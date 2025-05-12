<?php

namespace App\Exports;

use App\Models\Amount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AmountsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Amount::orderBy('id', 'asc')->get();
    }

    public function map($amount): array
{
    return [
        $amount->id,
            $amount->categories->name, // Categoria
            $amount->suppliers->name,  // Proveedor
            $amount->suppliers->ruc,   // RUC
            $amount->description,
            $amount->amount,
        $amount->date_init,
    ];
}

public function headings(): array
{
    // Este array define los encabezados en la fila 3
    return [
        ['LISTA DE EGRESOS', '', '', '', '', '', ''],  // Fila 1 con el título
        [],  // Fila 2 en blanco (espaciado entre el título y los encabezados)
        ['ID', 'Categoria', 'Proveedor', 'RUC', 'Descripción', 'Monto', 'Fecha_Init']  // Fila 3 con los encabezados
    ];
}

    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
{
    
    // Asegurando que el título "LISTA DE EGRESOS" esté centrado y con estilo
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1:G1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 14,  // Tamaño de la fuente para el título
        ],
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'CFE2F3'], // Color de fondo azul claro para el título
        ],
    ]);

    // Estilo para los encabezados de la tabla
    $sheet->getStyle('A3:G3')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'D9EAD3'], // Color de fondo para los encabezados
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    // Estilo para las filas de datos
    $sheet->getStyle('A4:G' . $sheet->getHighestRow())->applyFromArray([
        'alignment' => [
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    // Ajuste de las columnas para darles más espacio
    foreach (range('A', 'G') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Formato para la columna de monto
    $sheet->getStyle('F4:F' . $sheet->getHighestRow())->getNumberFormat()->setFormatCode('[$S/] #,##0.00');

    return [];
}
}
