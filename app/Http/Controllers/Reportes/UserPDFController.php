<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TCPDF;
use App\Models\User;

class UserPDFController extends Controller
{
    public function exportPdf()
    {
        $users = User::all();

        $usersArray = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'created_at' => $user->created_at->format('d/m/Y'),
                'status' => $user->status == 1 ? 'Activo' : 'Inactivo'
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Lista de Usuarios');
        $pdf->SetSubject('Reporte de Usuarios');

        // Configuración de márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // Eliminar la línea de encabezado (borde superior)
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);

        $pdf->AddPage();

        // Encabezado del PDF
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Lista de Usuarios', 0, 1, 'C');

        //$pdf->SetCellPadding(4);

        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242); 

        $header = ['ID', 'Nombre', 'Correo', 'Usuario', 'Fecha de creación', 'Estado'];
        $widths = [10, 30, 65, 30, 30, 25];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        // Imprimir los datos de cada servicio
        foreach ($usersArray as $user) {
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

            $pdf->MultiCell($widths[0], 10, ' ' . $user['id'] . ' ', 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $user['name'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $user['email'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $user['username'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $user['created_at'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 10, $user['status'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        return response($pdf->Output('usuarios.pdf', 'D'))->header('Content-Type', 'application/pdf');
    }
}
