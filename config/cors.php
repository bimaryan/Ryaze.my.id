<?php

/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS)
|--------------------------------------------------------------------------
|
| Origin yang diizinkan diambil dari API_CORS_ALLOWED_ORIGINS (.env),
| dipisah koma, contoh:
|
| API_CORS_ALLOWED_ORIGINS=https://ryaze.my.id,http://localhost:5173
|
*/

$origins = array_filter(array_map('trim', explode(',', env('API_CORS_ALLOWED_ORIGINS', 'https://ryaze.my.id'))));

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $origins ?: ['https://ryaze.my.id'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 3600,

    // Token dikirim via header Authorization (Bearer), jadi cookies tidak dibutuhkan.
    'supports_credentials' => false,

];
