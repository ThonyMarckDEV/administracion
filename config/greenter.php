<?php

use Greenter\Ws\Services\SunatEndpoints;

return [
    'ruc' => env('SUNAT_RUC', '20000000001'),
    'user' => env('SUNAT_USER', 'MODDATOS'),
    'password' => env('SUNAT_PASSWORD', 'moddatos'),

    // Interpretamos el string del .env y devolvemos la constante correspondiente
    'endpoint' => match (env('SUNAT_ENDPOINT', 'beta')) {
        'beta' => SunatEndpoints::FE_BETA,
        'produccion' => SunatEndpoints::FE_PRODUCCION,
        'homologacion' => SunatEndpoints::FE_HOMOLOGACION,
        default => SunatEndpoints::FE_BETA,
    },

    'certificate_path' => storage_path('app/public/certificates'),
];
