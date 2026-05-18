<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Wajib dipanggil untuk mencatat log

class WebhookController extends Controller
{
    public function handleWinpay(Request $request)
    {
        // 1. Tangkap semua data JSON yang dikirim oleh Winpay
        $payload = $request->all();

        // 2. Simpan data tersebut ke dalam file log Laravel 
        // (Bisa dicek di storage/logs/laravel.log nantinya)
        Log::info('--- WEBHOOK WINPAY MASUK ---');
        Log::info($payload);

        // 3. DI SINI NANTINYA TEMPAT KAMU UPDATE DATABASE
        // Contoh gambarannya nanti:
        // if ($request->status == 'PAID') {
        //     Order::where('reference', $request->order_id)->update(['status' => 'LUNAS']);
        // }

        // 4. WAJIB: Kembalikan respon 200 OK ke Winpay
        // Kalau tidak dibalas 200, Winpay akan mengira endpoint-mu mati 
        // dan akan mengirim data berulang-ulang (spam).
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received'
        ], 200);
    }
}