<?php
// config/greenter.php

return [
    'ruc' => env('SUNAT_RUC', '20000000001'),
    'user' => env('SUNAT_USER', 'MODDATOS'),
    'password' => env('SUNAT_PASSWORD', 'moddatos'),
    'endpoint' => env('SUNAT_ENDPOINT', \Greenter\Ws\Services\SunatEndpoints::FE_BETA),
    'certificate_path' => storage_path('app/public/certificates/certificate.pem'),
    'storage_path' => storage_path('app/public/comprobantes'),
];