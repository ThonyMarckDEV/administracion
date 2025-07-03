<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\Request;
use TCPDF;

class PeriodPDFController extends Controller
{
    public function exportPDF()
    {
        $periods = Period::orderBy('id', 'asc')->get();

        $periodsArray = $periods->map(function ($period) {
            return [
                'id' => $period->id,
                'name' => $period->name,
                'description' => $period->description,
                'state' => $period->state ? 'Activo' : 'Inactivo'
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Periodos');
        $pdf->SetSubject('Reporte de Periodos');

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
        $pdf->Cell(0, 20, 'Lista de Servicios', 0, 1, 'C');

        //$pdf->SetCellPadding(4);

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242); 

        $header = ['ID', 'Nombre', 'Descripción','Estado'];
        $widths = [10, 40, 40, 40];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada servicio
        foreach ($periodsArray as $period) {
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

            $pdf->MultiCell($widths[0], 10, $period['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $period['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $period['description'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $period['state'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        // Detenemos cualquier salida previa
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generamos el PDF como string en memoria
        $pdfOutput = $pdf->Output('periodos.pdf', 'S'); // "S" = string, no lo imprime directo
        
        // Laravel se encarga del response
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="periodos.pdf"');
    }
}