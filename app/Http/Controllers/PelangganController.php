<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; 
use App\Models\Order; // Pastikan model Order di-import

class PelangganController extends Controller
{
    // 1. TAMPILAN MENU PELANGGAN
    public function index($meja)
    {
        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')
                    ->select('menus.*', 'categories.name as category_name')
                    ->where('is_active', true)
                    ->get();

        return view('pelanggan.index', compact('menus', 'meja'));
    }

    // 2. PROSES SIMPAN PESANAN (CHECKOUT)
    public function store(Request $request) 
    {
        $request->validate([
            'cart_data' => 'required',
            'table_id' => 'required',
            'payment_method' => 'required'
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $cart = json_decode($request->cart_data, true);
        $totalPrice = collect($cart)->sum('subtotal');

        $order = new Order();
        $order->order_number = $orderNumber;
        $order->table_id = $request->table_id;
        $order->total_price = $totalPrice;
        $order->payment_method = $request->payment_method;
        
        // KUNCI: Status 0 (Menunggu Bayar) agar belum masuk ke layar dapur koki
        $order->order_status_id = 4; 
        $order->save();

        foreach ($cart as $item) {
            $order->orderItems()->create([
                'menu_id' => $item['menu_id'],
                'quantity' => $item['qty'],
                'subtotal' => $item['subtotal'],
                'notes' => $item['notes'] ?? '-'
            ]);
        }

        // LOGIKA PEMBAYARAN ONLINE (WINPAY)
        if ($request->payment_method == 'Winpay') {
            $winpayService = new \App\Services\WinpayService();
            $paymentUrl = $winpayService->createTransaction($order);
            
            if ($paymentUrl) {
                return redirect($paymentUrl);
            }
            return back()->with('error', 'Gagal terhubung ke Winpay.');
        }

        // JIKA TUNAI: Arahkan ke halaman sukses/menunggu pembayaran
        return redirect()->route('pelanggan.success', $order->id);
    }

    // 3. HALAMAN INSTRUKSI/STATUS BAYAR
    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('pelanggan.success', compact('order'));
    }
}