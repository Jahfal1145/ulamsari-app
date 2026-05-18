<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\WebhookController;

Route::post('/winpay/callback', function (Request $request) {
    // Mencatat data yang masuk ke storage/logs/laravel.log
    Log::info('Webhook Winpay Masuk:', $request->all());

    // Membalas pesan ke Winpay
    return response()->json([
        'status' => 'success',
        'message' => 'Laporan diterima aplikasi Ulam Sari'
    ], 200);
});

// Rute ini otomatis akan memiliki prefix /api
// Jadi URL lengkapnya nanti: https://domain-kamu.com/api/winpay/callback
Route::post('/winpay/callback', [WebhookController::class, 'handleWinpay']);