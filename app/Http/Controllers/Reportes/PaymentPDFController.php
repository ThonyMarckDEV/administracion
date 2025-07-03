<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use TCPDF;

class PaymentPDFController extends Controller
{
    public function exportPDF()
    {
        $payments = Payment::with(['customer', 'paymentPlan.service', 'paymentPlan.period'])
            ->orderBy('id', 'asc')
            ->get();

        $paymentsArray = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'customer' => $payment->customer->name ?? '---',
                'service' => $payment->paymentPlan->service->name ?? '---',
                'period' => $payment->paymentPlan->period->name ?? '---',
                'amount' => number_format($payment->amount, 2),
                'payment_date' => optional($payment->payment_date)->format('d-m-Y') ?? '---',
                'method' => ucfirst($payment->payment_method),
                'reference' => $payment->reference ? strtoupper($payment->reference) : '---',
                'status' => ucfirst($payment->status),
            ];
        })->toArray();

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Laravel');
        $pdf->SetTitle('Pagos');
        $pdf->SetSubject('Reporte de Pagos');

        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetHeaderData('', 0, '', '', [0, 0, 0], [255, 255, 255]);
        $pdf->setFooterData([0, 0, 0], [255, 255, 255]);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 15, 'Lista de Pagos', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(242, 242, 242);

        // Encabezados y anchos ajustados
        $header = ['ID', 'Cliente', 'Servicio', 'Periodo', 'Monto', 'Fecha', 'Método', 'Ref.', 'Estado'];
        $widths = [8, 28, 38, 18, 15, 20, 20, 23, 20];

        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 8, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 9);

        foreach ($paymentsArray as $payment) {
            if ($pdf->GetY() > 260) {
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(242, 242, 242);
                foreach ($header as $i => $col) {
                    $pdf->MultiCell($widths[$i], 8, $col, 1, 'C', 1, 0);
                }
                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 9);
            }

            $pdf->MultiCell($widths[0], 8, $payment['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[1], 8, $payment['customer'], 1, 'L', 0, 0);
            $pdf->MultiCell($widths[2], 8, $payment['service'], 1, 'L', 0, 0);
            $pdf->MultiCell($widths[3], 8, $payment['period'], 1, 'C', 0, 0);
            $pdf->Cell($widths[4], 8, 'S/ ' . $payment['amount'], 1, 0, 'R');
            $pdf->MultiCell($widths[5], 8, $payment['payment_date'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[6], 8, $payment['method'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[7], 8, $payment['reference'], 1, 'C', 0, 0);
            $pdf->MultiCell($widths[8], 8, $payment['status'], 1, 'C', 0, 0);
            $pdf->Ln();
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdfOutput = $pdf->Output('pagos.pdf', 'S');

        return response($pdfOutput)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="pagos.pdf"');
    }
}
