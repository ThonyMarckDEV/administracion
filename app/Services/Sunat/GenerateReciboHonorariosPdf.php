<?php

namespace App\Services\Sunat;

use App\Models\MyCompany;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GenerateReciboHonorariosPdf
{
    public function generate(array $data): string
    {
        try {
            // Validate required data
            $requiredFields = ['razon_social', 'ruc', 'service', 'monto', 'retention', 'monto_neto', 'fecha_emision', 'hora_emision', 'doc_series', 'doc_correlative'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new \Exception("Missing or empty required field: $field");
                }
            }

            // Fetch company data
            $company = MyCompany::first();
            if (!$company) {
                throw new \Exception('No company data found in MyCompany table');
            }

            // Load and encode logo
            $logoPath = public_path('images/logo.png');
            if (!File::exists($logoPath)) {
                throw new \Exception('Logo file not found at: ' . $logoPath);
            }
            $logoContent = File::get($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoContent);

            // Load HTML template
            $templatePath = resource_path('views/templates/recibo_por_honorarios.html');
            if (!File::exists($templatePath)) {
                throw new \Exception('Template file not found at: ' . $templatePath);
            }
            $html = File::get($templatePath);
            Log::debug('Loaded HTML template', ['length' => strlen($html)]);

            // Format company address
            $companyAddress = implode(', ', array_filter([
                $company->direccion,
                $company->urbanizacion,
                $company->distrito,
                $company->provincia,
                $company->departamento,
            ]));

            // Format document number
            $docNumber = sprintf('%s-%08d', $data['doc_series'], $data['doc_correlative']);

            // Replace placeholders with dynamic data
            $replacements = [
                '{{logoBase64}}' => $logoBase64,
                '{{company_razon_social}}' => htmlspecialchars($company->razon_social, ENT_QUOTES, 'UTF-8'),
                '{{company_ruc}}' => htmlspecialchars($company->ruc, ENT_QUOTES, 'UTF-8'),
                '{{company_direccion}}' => htmlspecialchars($companyAddress, ENT_QUOTES, 'UTF-8'),
                '{{company_telefono}}' => htmlspecialchars($company->telefono ?? '(01) 123-4567', ENT_QUOTES, 'UTF-8'),
                '{{company_email}}' => htmlspecialchars($company->email ?? 'contacto@solucionesingtj.com', ENT_QUOTES, 'UTF-8'),
                '{{razon_social}}' => htmlspecialchars($data['razon_social'], ENT_QUOTES, 'UTF-8'),
                '{{ruc}}' => htmlspecialchars($data['ruc'], ENT_QUOTES, 'UTF-8'),
                '{{service}}' => htmlspecialchars($data['service'], ENT_QUOTES, 'UTF-8'),
                '{{monto}}' => number_format($data['monto'], 2, '.', ''),
                '{{retention}}' => number_format($data['retention'], 2, '.', ''),
                '{{monto_neto}}' => number_format($data['monto_neto'], 2, '.', ''),
                '{{fecha_emision}}' => htmlspecialchars($data['fecha_emision'], ENT_QUOTES, 'UTF-8'),
                '{{hora_emision}}' => htmlspecialchars($data['hora_emision'], ENT_QUOTES, 'UTF-8'),
                '{{doc_number}}' => htmlspecialchars($docNumber, ENT_QUOTES, 'UTF-8'),
            ];
            $html = str_replace(array_keys($replacements), array_values($replacements), $html);
            Log::debug('HTML after replacements', ['html' => substr($html, 0, 500)]);

            // Check for backticks
            if (strpos($html, '`') !== false) {
                throw new \Exception('Invalid backtick (`) found in HTML template');
            }

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

            return $pdfContent;
        } catch (\Throwable $th) {
            Log::error('Error generating PDF', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'data' => $data,
            ]);
            throw $th;
        }
    }
}
