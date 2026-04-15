<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DapurController extends Controller
{
    public function index()
    {
        // Ambil order dengan status 1 (Pending), 2 (Cooking), 3 (Ready)
        $orders = Order::whereIn('order_status_id', [1, 2, 3])
                       ->orderBy('created_at', 'asc')
                       ->get();

        // Ambil detail pesanan (makanan, qty, notes) dari tabel order_items & menus
        foreach ($orders as $order) {
            $order->detail_pesanan = DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->where('order_items.order_id', $order->id)
                ->select(
                    'order_items.quantity as qty',
                    'order_items.notes',
                    'menus.name'
                )
                ->get();
        }

        return view('dapur.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Logika Status: 1=Pending, 2=Cooking, 3=Ready, 4=Selesai/Diantar
        if ($order->order_status_id == 1) {
            $order->order_status_id = 2;
        } elseif ($order->order_status_id == 2) {
            $order->order_status_id = 3;
        } else {
            $order->order_status_id = 4; // Hilang dari layar dapur
        }
        
        $order->save();
        return redirect()->back();
    }
}