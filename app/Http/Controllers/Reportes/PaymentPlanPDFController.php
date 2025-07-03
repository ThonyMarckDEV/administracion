<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use Illuminate\Http\Request;
use TCPDF;

class PaymentPlanPDFController extends Controller
{
    public function exportPDF()
    {
        $plans = PaymentPlan::with(['service', 'customer', 'period'])->orderBy('id', 'asc')->get();

        $plansArray = $plans->map(function ($plan) {
            return [
                'id' => $plan->id,
                'service' => $plan->service->name ?? '---',
                'customer' => $plan->customer->name ?? '---',
                'period' => $plan->period->name ?? '---',
                'payment_type' => $plan->payment_type ? 'Anual' : 'Mensual',
                'amount' => number_format($plan->amount, 2),
                'duration' => $plan->duration,
                'state' => $plan->state ? 'Activo' : 'Inactivo',
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Planes de Pago');
        $pdf->SetSubject('Reporte de Planes de Pago');

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);
        $pdf->setFooterData([0,0,0], [255,255,255]);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 20, 'Planes de Pago', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(242, 242, 242);

        $header = ['ID', 'Servicio', 'Cliente', 'Periodo', 'Tipo', 'Monto', 'Duración', 'Estado'];
        $widths = [10, 35, 35, 25, 20, 20, 20, 20];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);

        foreach ($plansArray as $plan) {
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
            $pdf->MultiCell($widths[0], 10, $plan['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 10, $plan['service'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[2], 10, $plan['customer'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[3], 10, $plan['period'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[4], 10, $plan['payment_type'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[5], 10, 'S/ ' . $plan['amount'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 10, $plan['duration'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 10, $plan['state'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdfOutput = $pdf->Output('planes_pago.pdf', 'S');

        return response($pdfOutput)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="planes_pago.pdf"');
    }
}
