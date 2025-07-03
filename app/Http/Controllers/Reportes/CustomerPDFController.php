<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use TCPDF;

class CustomerPDFController extends Controller
{
    public function exportPDF()
    {
        $customers = Customer::orderBy('name', 'asc')->get();

        $customersArray = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'codigo' => $customer->codigo,
                'client_type' => $customer->clienteType->name ?? 'Sin tipo',
                'created_at' => $customer->created_at->format('d-m-Y H:i:s'),
                'state' => $customer->state ? 'Activo' : 'Inactivo'
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Clientes');
        $pdf->SetSubject('Reporte de Clientes');

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);
        $pdf->setFooterData([0, 0, 0], [255, 255, 255]);

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Clientes', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);

        $header = ['ID', 'Nombre', 'Código', 'Tipo de cliente','Fecha de creación', 'Estado'];
        $widths = [15, 50, 30, 30, 30, 30];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        foreach ($customersArray as $customer) {
            if ($pdf->GetY() > 260) {
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
            }
            $pdf->SetFont('helvetica', '', 10);

            $pdf->MultiCell($widths[0], 10, $customer['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $customer['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $customer['codigo'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $customer['client_type'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $customer['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $customer['state'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        // Detenemos cualquier salida previa
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // Generamos el PDF como string en memoria
        $pdfOutput = $pdf->Output('clientes.pdf', 'S'); // "S" = string, no lo imprime directo
        
        // Laravel se encarga del response
        return response($pdfOutput)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="clientes.pdf"');
    }
}
