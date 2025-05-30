<?php

namespace App\Services\Sunat;

use Greenter\Model\Sale\Invoice;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class GeneratePdf
{
    /**
     * Generate PDF content for factura or boleta with an elegant design.
     *
     * @param Invoice $invoice
     * @param string $path
     * @return string Absolute path to the saved PDF
     * @throws \Exception
     */
    public function generate(Invoice $invoice, string $path): string
    {
        $docType = $invoice->getTipoDoc() === '01' ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA';
        $company = $invoice->getCompany();
        $client = $invoice->getClient();
        $items = $invoice->getDetails();
        $legend = $invoice->getLegends()[0];

        // Load and encode logo
        $logoPath = public_path('images/logo.png');
        if (!File::exists($logoPath)) {
            throw new \Exception('Logo file not found at: ' . $logoPath);
        }
        $logoContent = File::get($logoPath);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoContent);

        // HTML template with professional black and white design
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>$docType</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1a1a1a;
            background: #ffffff;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: #ffffff;
            min-height: 297mm;
            position: relative;
        }
        
        /* Header Section */
        .header {
            border-bottom: 2px solid #000000;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }
        
        .company-details {
            font-size: 11px;
            color: #333333;
            line-height: 1.3;
        }
        
        .company-details p {
            margin-bottom: 2px;
        }
        
        /* Document Type Box */
        .doc-type-box {
            border: 2px solid #000000;
            padding: 15px;
            text-align: center;
            min-width: 180px;
            background: #f8f8f8;
        }
        
        .doc-type-title {
            font-size: 14px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .doc-series {
            font-size: 20px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        
        .doc-ruc {
            font-size: 11px;
            color: #333333;
            font-weight: 500;
        }
        
        /* Info Sections */
        .info-section {
            padding: 0 30px;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }
        
        .info-block h3 {
            font-size: 12px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e0e0e0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-block p {
            font-size: 11px;
            margin-bottom: 3px;
            color: #333333;
        }
        
        .info-block strong {
            font-weight: 500;
            color: #000000;
            display: inline-block;
            min-width: 80px;
        }
        
        /* Table Styles */
        .table-container {
            padding: 0 30px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        th {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: none;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e8e8e8;
            color: #333333;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Totals Section */
        .totals-section {
            padding: 0 30px;
            margin-top: 25px;
        }
        
        .totals-container {
            display: flex;
            justify-content: flex-end;
        }
        
        .totals-box {
            border: 1px solid #000000;
            padding: 15px;
            min-width: 280px;
            background: #f8f8f8;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 11px;
        }
        
        .total-row.final {
            border-top: 2px solid #000000;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 13px;
            font-weight: 700;
        }
        
        .total-label {
            color: #333333;
            font-weight: 500;
        }
        
        .total-value {
            color: #000000;
            font-weight: 600;
            text-align: right;
            min-width: 80px;
        }
        
        /* Legend and Footer */
        .legend-section {
            padding: 20px 30px;
            margin-top: 25px;
            border-top: 1px solid #e0e0e0;
        }
        
        .legend-title {
            font-size: 11px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .legend-text {
            font-size: 10px;
            color: #555555;
            line-height: 1.4;
        }
        
        .footer {
            position: absolute;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 9px;
            color: #666666;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        
        /* Currency formatting */
        .currency {
            font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
            font-weight: 500;
        }
        
        /* Print optimization */
        @media print {
            body { margin: 0; }
            .container { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Company Info and Document Type -->
        <div class="header">
            <div class="company-info">
                <img src="$logoBase64" alt="Company Logo" class="company-logo">
                <div class="company-name">{$company->getRazonSocial()}</div>
                <div class="company-details">
                    <p>{$company->getAddress()->getDireccion()}</p>
                    <p>{$company->getAddress()->getDistrito()}</p>
                    <p>Teléfono: (01) 000-0000 | Email: contacto@empresa.com</p>
                </div>
            </div>
            <div class="doc-type-box">
                <div class="doc-type-title">$docType</div>
                <div class="doc-series">{$invoice->getSerie()}-{$invoice->getCorrelativo()}</div>
                <div class="doc-ruc">RUC: {$company->getRuc()}</div>
            </div>
        </div>

        <!-- Document and Client Information -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-block">
                    <h3>Datos del Comprobante</h3>
                    <p><strong>Fecha Emisión:</strong> {$invoice->getFechaEmision()->format('d/m/Y')}</p>
                    <p><strong>Hora Emisión:</strong> {$invoice->getFechaEmision()->format('H:i:s')}</p>
                    <p><strong>Moneda:</strong> {$invoice->getTipoMoneda()}</p>
                    <p><strong>Tipo de Operación:</strong> Venta</p>
                </div>
                <div class="info-block">
                    <h3>Datos del Cliente</h3>
                    <p><strong>Tipo Doc:</strong> {$client->getTipoDoc()}</p>
                    <p><strong>Número:</strong> {$client->getNumDoc()}</p>
                    <p><strong>Razón Social:</strong> {$client->getRznSocial()}</p>
                    <p><strong>Dirección:</strong> -</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Código</th>
                        <th>Descripción</th>
                        <th style="width: 60px;" class="text-center">Cant.</th>
                        <th style="width: 80px;" class="text-right">P. Unit.</th>
                        <th style="width: 70px;" class="text-right">IGV</th>
                        <th style="width: 80px;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
HTML;

        foreach ($items as $item) {
            $html .= <<<HTML
                    <tr>
                        <td>{$item->getCodProducto()}</td>
                        <td>{$item->getDescripcion()}</td>
                        <td class="text-center">{$item->getCantidad()}</td>
                        <td class="text-right currency">{$item->getMtoPrecioUnitario()}</td>
                        <td class="text-right currency">{$item->getIgv()}</td>
                        <td class="text-right currency">{$item->getMtoValorVenta()}</td>
                    </tr>
HTML;
        }

        $html .= <<<HTML
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-container">
                <div class="totals-box">
                    <div class="total-row">
                        <span class="total-label">Valor Venta:</span>
                        <span class="total-value currency">{$invoice->getValorVenta()}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">IGV (18%):</span>
                        <span class="total-value currency">{$invoice->getMtoIGV()}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Total Impuestos:</span>
                        <span class="total-value currency">{$invoice->getTotalImpuestos()}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-value currency">{$invoice->getSubTotal()}</span>
                    </div>
                    <div class="total-row final">
                        <span class="total-label">TOTAL A PAGAR:</span>
                        <span class="total-value currency">{$invoice->getMtoImpVenta()}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend Section -->
        <div class="legend-section">
            <div class="legend-title">Observaciones</div>
            <div class="legend-text">{$legend->getValue()}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este documento ha sido generado electrónicamente y tiene validez legal según la normativa vigente de SUNAT</p>
            <p>Representación impresa del comprobante electrónico generado desde el sistema de {$company->getRazonSocial()}</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Render HTML to PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $pdfContent = $dompdf->output();
        if ($pdfContent === false) {
            throw new \Exception('Failed to generate PDF');
        }

        // Save PDF
        Storage::put($path, $pdfContent);

        return Storage::path($path);
    }
}