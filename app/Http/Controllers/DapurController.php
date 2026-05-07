<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // 👈 Pastikan baris ini ada di paling atas
use App\Models\Order; // 👈 Sesuaikan dengan nama Model pesananmu

class DapurController extends Controller
{
    // 1. FUNGSI INDEX (Perhatikan bagian dalam kurung harus ada Request $request)
    public function index(Request $request) 
    {
        $jenis = $request->query('jenis', 'semua'); // Default 'semua'

        $query = Order::with('orderItems'); // Sesuaikan dengan modelmu

        if ($jenis == 'dine-in') {
            // Pastikan 'jenis_pesanan' sesuai dengan nama kolom di database kamu
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

        $orders = $query->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    // 2. FUNGSI UPDATE STATUS (Jangan dihapus, ini yang udah kamu buat sebelumnya)
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