<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WinpayService
{
    protected $merchantId;
    protected $secretKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->merchantId = env('WINPAY_MERCHANT_ID'); // Taruh di file .env nanti
        $this->secretKey = env('WINPAY_SECRET_KEY');
        $this->apiUrl = 'https://api-ent.winpay.id/v1/order'; // URL Sandbox/Production
    }

    public function createTransaction($order)
    {
        // KERANGKA DATA REQUEST WINPAY
        // Sesuaikan dengan dokumentasi teknis Winpay yang kamu punya
        $payload = [
            'merchant_id'   => $this->merchantId,
            'order_id'      => $order->order_number,
            'amount'        => $order->total_price,
            'customer_name' => $order->customer_name ?? 'Pelanggan Meja ' . $order->table_id,
            // 'signature'  => $this->generateSignature($order), 
            // Winpay biasanya minta signature HMAC-SHA256
        ];

        // Contoh simulasi hit API
        // $response = Http::post($this->apiUrl, $payload);
        
        // if ($response->successful()) {
        //    return $response->json()['redirect_url'];
        // }

        return null; // Sementara return null karena API belum diaktifkan
    }
}