<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
<<<<<<< HEAD
use App\Models\Order; 

class DapurController extends Controller
{
    // 1. FUNGSI INDEX (Sudah Diperbaiki)
    public function index(Request $request) 
=======
use App\Models\Order;

class DapurController extends Controller
{
    public function index(Request $request)
>>>>>>> 738701d9697ce5930fb6c53130bde905d8583124
    {
        $jenis = $request->query('jenis', 'semua');

<<<<<<< HEAD
        // PERBAIKAN 1: Panggil 'orderItems.menu' agar nama menu tidak gaib
        // PERBAIKAN 2: Sembunyikan pesanan yang statusnya 3 (Selesai)
        $query = Order::with(['orderItems.menu'])
                      ->where('order_status_id', '!=', 3);
=======
        // Kita ganti kurung siku jadi array() biar nggak kehapus pas dicopas
        $query = Order::with(array('orderItems.menu')); 
>>>>>>> 738701d9697ce5930fb6c53130bde905d8583124

        // Filter tipe pesanan (Berdasarkan tombol filter di atas)
        if ($jenis == 'dine-in') {
            $query->where('jenis_pesanan', 'dine-in'); 
        } elseif ($jenis == 'take-away') {
            $query->where('jenis_pesanan', 'take-away');
        }

<<<<<<< HEAD
        // PERBAIKAN 3: Urutkan dari pesanan yang paling lama masuk (asc) biar koki masak sesuai antrean
        $orders = $query->orderBy('id', 'asc')->get();
=======
        // Kita ganti kurung siku jadi array(1, 2)
        $orders = $query->whereIn('order_status_id', array(1, 2))
                        ->orderBy('created_at', 'asc')
                        ->get();
>>>>>>> 738701d9697ce5930fb6c53130bde905d8583124

        return view('dapur.index', compact('orders', 'jenis'));
    }

<<<<<<< HEAD
    // 2. FUNGSI UPDATE STATUS (Sudah Aman)
=======
>>>>>>> 738701d9697ce5930fb6c53130bde905d8583124
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