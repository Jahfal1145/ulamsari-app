<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class PelangganController extends Controller
{
    // TAMPILAN MENU
    public function index($meja)
    {
        // 1. Ambil Menu
        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')
            ->select('menus.*', 'categories.name as category_name')
            ->where('is_active', true)
            ->get();

        // 2. ★ AMBIL KATEGORI DARI DATABASE (Ini yang bikin kategori muncul!)
        $categories = DB::table('categories')->get();

        // 3. Kirim menus, meja, dan categories ke view
        return view('pelanggan.index', compact('menus', 'meja', 'categories'));
    }

    // PROSES CHECKOUT
    public function store(Request $request)
    {
        $request->validate([
            'cart_data' => 'required',
            'table_id' => 'required',
            'payment_method' => 'required',
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $cart = json_decode($request->cart_data, true);
        $totalPrice = collect($cart)->sum('subtotal');

        $order = new Order();
        $order->order_number = $orderNumber;
        $order->table_id = $request->table_id;
        $order->total_price = $totalPrice;
        $order->payment_method = $request->payment_method;
        $order->customer_name = strtoupper($request->customer_name);
        $order->phone_number = $request->phone_number;
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

        // PEMBAYARAN ONLINE
        if ($request->payment_method == 'Winpay') {
            $winpayService = new \App\Services\WinpayService();
            $paymentUrl = $winpayService->createTransaction($order);

            if ($paymentUrl) {
                return redirect($paymentUrl);
            }
            return back()->with('error', 'Gagal terhubung ke Winpay.');
        }

        // PEMBAYARAN TUNAI
        return redirect()->route('pelanggan.success', $order->id);
    }

    // HALAMAN SUKSES
    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('pelanggan.success', compact('order'));
    }
}