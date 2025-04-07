<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use TCPDF;

class ServicePDFController extends Controller
{
    public function exportPDF()
    {
        $services = Service::orderBy('id', 'asc')->get();

        $servicesArray = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'cost' => $service->cost,
                'ini_date' => $service->ini_date,
                'state' => $service->state === 'activo' ? 'Activo' : ($service->state === 'pendiente' ? 'Pendiente' : 'Inactivo'),
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Servicios');
        $pdf->SetSubject('Reporte de Servicios');

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

        $header = ['ID', 'Nombre', 'Costo', 'Fecha de Inicio', 'Estado'];
        $widths = [10, 60, 40, 40, 40];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada servicio
        foreach ($servicesArray as $service) {
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

            $pdf->MultiCell($widths[0], 10, $service['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $service['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, 'S/ ' . number_format($service['cost'], 2), 1, 'C', 0, 0);            
            $pdf->MultiCell($widths[3], 10, $service['ini_date'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $service['state'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        // Detenemos cualquier salida previa
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generamos el PDF como string en memoria
        $pdfOutput = $pdf->Output('servicios.pdf', 'S'); // "S" = string, no lo imprime directo
        
        // Laravel se encarga del response
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="servicios.pdf"');
    }
}