<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 

class DapurController extends Controller
{
    public function index(Request $request) 
    {
        $jenis = $request->query('jenis', 'semua'); 

        // KUNCI GERBANG: Sudah diisi dengan array
        $query = Order::with(['orderItems.menu'])
              ->whereIn('order_status_id', [1,2]);

        if ($jenis == 'dine-in') {
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

        $orders = $query->orderBy('id', 'asc')->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    public function updateStatus($id)
    {
        $order = Order::find($id);

        if ($order->order_status_id == 1) {
            $order->order_status_id = 2; // Sedang dimasak
        } elseif ($order->order_status_id == 2) {
            $order->order_status_id = 3; // Selesai
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}