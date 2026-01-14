<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de dominios para tienda pública y sistema interno
    |
    */

    'web' => [
        'domain' => env('APP_URL', 'http://www.playnow.local'),
        'subdomain' => 'www',
    ],

    'sistema' => [
        'domain' => env('SISTEMA_URL', 'http://sistema.playnow.local'),
        'subdomain' => 'sistema',
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Validation
    |--------------------------------------------------------------------------
    |
    | Lista de subdominios permitidos
    |
    */

    'allowed_subdomains' => [
        'www',
        'sistema',
    ],

];