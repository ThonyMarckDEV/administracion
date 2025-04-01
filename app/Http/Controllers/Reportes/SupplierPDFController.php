<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use TCPDF;

class SupplierPDFController extends Controller
{
    public function exportPdf()
    {
        $suppliers = Supplier::all();

        $suppliersArray = $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'ruc' => $supplier->ruc,
                'address' => $supplier->address,
                'state' => $supplier->state == 1 ? 'Activo' : 'Inactivo'
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Proveedores');
        $pdf->SetSubject('Reporte de Proveedores');
        
        // Configuración de márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // Eliminar la línea de encabezado (borde superior)
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);

        $pdf->AddPage();

        // Encabezado del PDF
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Proveedores', 0, 1, 'C');

        //$pdf->SetCellPadding(4);

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242); 

        $header = ['ID', 'Nombre', 'RUC', 'Dirección', 'Estado'];
        $widths = [10, 40, 30, 80, 30];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada proveedor
        foreach ($suppliersArray as $supplier) {
            if ($pdf->GetY() > 260) { // Si la posición Y está cerca del final de la página
                $pdf->AddPage(); // Añadir una nueva página
                // Imprimir los encabezados nuevamente en la nueva página
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(242, 242, 242); 
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell($widths[0], 10, ' ' . $supplier['id'] . ' ', 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $supplier['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $supplier['ruc'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $supplier['address'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $supplier['state'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        return response($pdf->Output('proveedores.pdf', 'D'))->header('Content-Type', 'application/pdf');
    }
}