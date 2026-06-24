<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentCallbackController;

// Rute ini otomatis akan memiliki awalan /api/ di depannya
Route::post('pakasir/webhook', [PaymentCallbackController::class, 'handleWebhook']);
