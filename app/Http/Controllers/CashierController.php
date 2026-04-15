<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')
                    ->select('menus.*', 'categories.name as category_name')
                    ->where('is_active', true)
                    ->get();
        $tables = Table::all();

        // Ambil semua pending orders beserta items-nya
    $pendingOrders = Order::with(['orderItems.menu'])
                        ->where('order_status_id', 1)
                        ->get()
                        ->keyBy('table_id'); // key by table_id biar gampang dicari di JS

    return view('kasir.index', compact('menus', 'tables', 'pendingOrders'));
    }
    
    public function store(Request $request)
    {
        $cart = json_decode($request->cart_data, true);

        // Proteksi 1: Cek Keranjang
        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Pilih menu dulu rek!');
        }

        // Proteksi 2: Cek Meja
        if (!$request->table_id) {
            return back()->with('error', 'Meja belum dipilih, silakan pilih dulu!');
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'table_id' => $request->table_id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_price' => collect($cart)->sum('subtotal'),
                'order_status_id' => 1,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Pesanan Meja ' . $request->table_id . ' berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }
}