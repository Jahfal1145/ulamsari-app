<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Pastikan ini sesuai dengan nama model kamu

class DapurController extends Controller
{
    // 1. FUNGSI TAMPILKAN DAPUR (Dine-in & Take-away)
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'semua'); // Default 'semua'

        $query = Order::with('detail_pesanan'); // Sesuaikan relasi dengan modelmu

        if ($jenis == 'dine-in') {
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

        $orders = $query->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    // 2. FUNGSI UPDATE STATUS PESANAN
    public function updateStatus($id)
    {
        $order = Order::find($id);

        if ($order->order_status_id == 1) {
            $order->order_status_id = 2; // Proses masak
        } elseif ($order->order_status_id == 2) {
            $order->order_status_id = 3; // Selesai
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}