<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DapurController extends Controller
{
    public function index()
    {
        // Menampilkan status 1 (Pending) dan 2 (Cooking) saja
        $orders = Order::whereIn('order_status_id', [1, 2])
                       ->orderBy('created_at', 'asc')
                       ->get();

        // Ambil detail pesanan (makanan, qty, notes)
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

        // Perubahan status: 1 (Pending) -> 2 (Cooking) -> 3 (Ready)
        if ($order->order_status_id == 1) {
            $order->order_status_id = 2;
        } elseif ($order->order_status_id == 2) {
            $order->order_status_id = 3;
        }

        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}