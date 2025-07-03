<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use TCPDF;

class InvoicePDFController extends Controller
{
    public function exportPDF()
    {
        $invoices = Invoice::with(['payment.customer', 'payment.paymentPlan.service'])
            ->join('payments', 'invoices.payment_id', '=', 'payments.id')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->orderBy('customers.name', 'asc')
            ->select('invoices.*')
            ->get();

        $invoiceArray = $invoices->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'payment_id' => $invoice->payment_id,
                'document_type' => $invoice->document_type === 'B' ? 'Boleta' : 'Factura',
                'serie' => $invoice->serie_assigned,
                'correlative' => $invoice->correlative_assigned,
                'customer' => $invoice->payment->customer->name ?? '---',
                'service' => $invoice->payment->paymentPlan->service->name ?? '---',
                'amount' => 'S/ ' . number_format($invoice->payment->amount, 2),
                'date' => optional($invoice->payment->payment_date)->format('d-m-Y') ?? '---',
                'method' => ucfirst($invoice->payment->payment_method),
                'sunat' => ucfirst($invoice->sunat ?? 'Pendiente'),
            ];
        });

        $pdf = new TCPDF();
        $pdf->SetCreator('Laravel TCPDF');
        $pdf->SetAuthor('Sistema');
        $pdf->SetTitle('Comprobantes Electrónicos');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage('L'); // Horizontal

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Listado de Comprobantes Electrónicos', 0, 1, 'C');

        $header = ['ID', 'ID Pago', 'Tipo', 'Serie', 'Correlativo', 'Cliente', 'Servicio', 'Monto', 'Fecha', 'Método', 'SUNAT'];
        $widths = [5, 10, 15, 20, 22, 35, 35, 25, 22, 22, 20];

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        foreach ($header as $i => $col) {
            $pdf->MultiCell($widths[$i], 10, $col, 1, 'C', 1, 0);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 9);
        foreach ($invoiceArray as $inv) {
            foreach (array_values($inv) as $i => $val) {
                $pdf->MultiCell($widths[$i], 10, $val, 1, 'C', 0, 0);
            }
            $pdf->Ln();
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        return response($pdf->Output('comprobantes.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="comprobantes.pdf"');
    }
}
