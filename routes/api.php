<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentCallbackController;

// Rute ini otomatis akan memiliki awalan /api/ di depannya
Route::post('pakasir/webhook', [PaymentCallbackController::class, 'handleWebhook'])
    ->middleware('throttle:10,1'); // Maksimal 10 request per menit untuk mencegah bruteforce

// Supabase-like Database API
Route::any('/v1/db/{hashid}/{path?}', [\App\Http\Controllers\Api\DatabaseApiController::class, 'handle'])
    ->where('path', '.*');
