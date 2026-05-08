<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 

class DapurController extends Controller
{
    // 1. FUNGSI INDEX (Sudah Diperbaiki)
    public function index(Request $request) 
    {
        $jenis = $request->query('jenis', 'semua'); // Default 'semua'

        // PERBAIKAN 1: Panggil 'orderItems.menu' agar nama menu tidak gaib
        // PERBAIKAN 2: Sembunyikan pesanan yang statusnya 3 (Selesai)
        $query = Order::with(['orderItems.menu'])
                      ->where('order_status_id', '!=', 3);

        // Filter tipe pesanan (Berdasarkan tombol filter di atas)
        if ($jenis == 'dine-in') {
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

        // PERBAIKAN 3: Urutkan dari pesanan yang paling lama masuk (asc) biar koki masak sesuai antrean
        $orders = $query->orderBy('id', 'asc')->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    // 2. FUNGSI UPDATE STATUS (Sudah Aman)
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