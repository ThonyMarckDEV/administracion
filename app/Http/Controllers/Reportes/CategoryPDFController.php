<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use TCPDF;

class CategoryPDFController extends Controller
{
    public function exportPDF()
    {
        $categories = Category::orderBy('id', 'asc')->get();

        $categoriesArray = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'status' => $category->status == 1 ? 'Activo' : 'Inactivo'
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Categorías');
        $pdf->SetSubject('Reporte de Categorías');
        
        // Configuración de márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // Eliminar la línea de encabezado (borde superior)
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);

        // Personalizar el pie de página (eliminar línea predeterminada)
        $pdf->setFooterData(array(0,0,0), array(255,255,255));

        $pdf->AddPage();

        // Encabezado del PDF
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Categorías', 0, 1, 'C');

        //$pdf->SetCellPadding(4);

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242); 

        $header = ['ID', 'Nombre', 'Estado'];
        $widths = [30, 120, 40];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada categoría
        foreach ($categoriesArray as $category) {
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
            $pdf->MultiCell($widths[0], 10, $category['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $category['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $category['status'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        return response($pdf->Output('categorías.pdf', 'D'))->header('Content-Type', 'application/pdf');
    }
}
