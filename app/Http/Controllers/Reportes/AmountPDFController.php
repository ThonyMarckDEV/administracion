<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Amount;
use Illuminate\Http\Request;
use TCPDF;

class AmountPDFController extends Controller
{
    public function exportPDF()
    {
        $amounts = Amount::orderBy('id', 'asc')->get();

        $amountsArray = $amounts->map(function ($amount) {
            return [
                'id' => $amount->id,
                'category_name' => $amount->categories->name,
                'supplier_name' => $amount->suppliers->name,
                'ruc' => $amount->suppliers->ruc,                
                'description' => $amount->description,
                'amount' => $amount->amount,
                'date_init' => $amount->date_init,
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Egresos');
        $pdf->SetSubject('Reporte de Egresos');

        // Establecer la orientación a horizontal
        $pdf->SetPageOrientation('L'); // 'L' para horizontal

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
        $pdf->Cell(0, 20, 'Lista de Egresos', 0, 1, 'C');

        //$pdf->SetCellPadding(4);

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242); 

        $header = ['ID', 'Categoria', 'Proveedor', 'RUC', 'Descripción', 'Monto', 'Fecha_init'];
        $widths = [10, 40, 40, 50, 60, 40, 40];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();


        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada egreso
        foreach ($amountsArray as $amount) {
            if ($pdf->GetY() > 260) { // Si la posición Y está cerca del final de la página
                $pdf->AddPage(); // Añadir una nueva página
                // Imprimir los encabezados
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(242, 242, 242); 
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }
            $pdf->SetFont('helvetica', '', 10);

            $pdf->MultiCell($widths[0], 10, $amount['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $amount['category_name'], 1, 'C', 0, 0); 
            $pdf->MultiCell($widths[2], 10, $amount['supplier_name'], 1, 'C', 0, 0);  
            $pdf->MultiCell($widths[3], 10, $amount['ruc'], 1, 'C', 0, 0);  
            $pdf->MultiCell($widths[4], 10, $amount['description'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 10, 'S/ ' . number_format($amount['amount'], 2), 1, 'C', 0, 0);            
            $pdf->MultiCell($widths[6], 10, $amount['date_init'], 1, 'C', 0, 0);

            $pdf->Ln();
        }

        // Detenemos cualquier salida previa
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generamos el PDF como string en memoria
        $pdfOutput = $pdf->Output('egresos.pdf', 'S'); // "S" = string, no lo imprime directo
        
        // Laravel se encarga del response
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="egresos.pdf"');
    }
}
