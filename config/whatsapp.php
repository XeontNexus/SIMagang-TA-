<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway (Fonnte)
    |--------------------------------------------------------------------------
    |
    | Daftar di https://fonnte.com lalu salin token ke FONNTE_TOKEN di .env
    | Nomor WhatsApp yang terhubung di Fonnte akan mengirim pesan otomatis.
    |
    */

    'enabled' => env('WHATSAPP_ENABLED', true),

    'fonnte_token' => env('FONNTE_TOKEN'),

    'api_url' => env('FONNTE_API_URL', 'https://api.fonnte.com/send'),

];
