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

        // Ambil pesanan yang masih Pending (1)
        $pendingOrders = Order::with(['orderItems.menu'])
                        ->where('order_status_id', 1)
                        ->get()
                        ->groupBy('table_id'); 

        return view('kasir.index', compact('menus', 'tables', 'pendingOrders'));
    }
    
    public function store(Request $request)
    {
        $cart = json_decode($request->cart_data, true);

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Pilih menu dulu rek!');
        }

        if ($request->table_id === null || $request->table_id === '') {
            return back()->with('error', 'Meja belum dipilih, silakan pilih dulu!');
        }

        $paymentStatus = ($request->payment_type === 'now') ? 'Lunas' : 'Belum Lunas';
        $paymentMethod = $request->payment_method ?? 'Belum Bayar';

        DB::beginTransaction();
        try {
            $order = Order::create([
                'table_id' => $request->table_id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_price' => collect($cart)->sum('subtotal'),
                'order_status_id' => 1,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
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

            $successMsg = ($paymentStatus === 'Lunas') 
                ? 'Pesanan Meja ' . $request->table_id . ' dikirim. LUNAS (' . $paymentMethod . ')' 
                : 'Pesanan Meja ' . $request->table_id . ' dikirim. (BAYAR NANTI)';

            return back()->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }
}