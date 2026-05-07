<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class DapurController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'semua');

        // Kita ganti kurung siku jadi array() biar nggak kehapus pas dicopas
        $query = Order::with(array('orderItems.menu')); 

        if ($jenis == 'dine-in') {
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

        // Kita ganti kurung siku jadi array(1, 2)
        $orders = $query->whereIn('order_status_id', array(1, 2))
                        ->orderBy('created_at', 'asc')
                        ->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    public function updateStatus($id)
    {
        $order = Order::findOrFail($id);

        if ($order->order_status_id == 1) {
            $order->order_status_id = 2; // Mulai dimasak
        } elseif ($order->order_status_id == 2) {
            $order->order_status_id = 3; // Selesai dimasak
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}