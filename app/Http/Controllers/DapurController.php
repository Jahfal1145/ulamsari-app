<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 

class DapurController extends Controller
{
    public function index(Request $request) 
    {
        // Tangkap parameter 'jenis' dari URL (semua, dine-in, atau take-away)
        $jenis = $request->query('jenis', 'semua'); 

        // Query dasar: Ambil pesanan yang statusnya Masuk (1) atau Dimasak (2)
        $query = Order::where(function($q) {
                    $q->where('order_status_id', 1)
                      ->orWhere('order_status_id', 2);
                });

                    if ($jenis == 'dine-in') {
                $query->whereHas('orderItems', function($q) {
                    $q->whereRaw('LOWER(notes) NOT LIKE ?', ['%bungkus%'])
                    ->whereRaw('LOWER(notes) NOT LIKE ?', ['%takeaway%'])
                    ->whereRaw('LOWER(notes) NOT LIKE ?', ['%take-away%']);
                })->with(['orderItems' => function($q) {
                    $q->whereRaw('LOWER(notes) NOT LIKE ?', ['%bungkus%'])
                    ->whereRaw('LOWER(notes) NOT LIKE ?', ['%takeaway%'])
                    ->whereRaw('LOWER(notes) NOT LIKE ?', ['%take-away%']);
                }, 'orderItems.menu']);

            } elseif ($jenis == 'take-away') {
                $query->whereHas('orderItems', function($q) {
                    $q->where(function($q2) {
                        $q2->whereRaw('LOWER(notes) LIKE ?', ['%bungkus%'])
                        ->orWhereRaw('LOWER(notes) LIKE ?', ['%takeaway%'])
                        ->orWhereRaw('LOWER(notes) LIKE ?', ['%take-away%']);
                    });
                })->with(['orderItems' => function($q) {
                    $q->where(function($q2) {
                        $q2->whereRaw('LOWER(notes) LIKE ?', ['%bungkus%'])
                        ->orWhereRaw('LOWER(notes) LIKE ?', ['%takeaway%'])
                        ->orWhereRaw('LOWER(notes) LIKE ?', ['%take-away%']);
                    });
                }, 'orderItems.menu']);

            } else {
                $query->with(['orderItems.menu']);
            }

        $orders = $query->orderBy('id', 'asc')->get();

        return view('dapur.index', compact('orders', 'jenis'));
    }

    // FUNGSI UNTUK MENGUBAH STATUS PESANAN (MULAI MASAK -> SELESAI)
    public function updateStatus($id)
    {
        // 1. Cari pesanan berdasarkan ID-nya
        $order = Order::findOrFail($id);

        // 2. Cek status saat ini dan ubah ke tahap selanjutnya
        if ($order->order_status_id == 1) {
            // Jika status masih 1 (Baru Masuk), ubah jadi 2 (Sedang Dimasak)
            $order->order_status_id = 2;
        } elseif ($order->order_status_id == 2) {
            // Jika status sudah 2 (Sedang Dimasak), ubah jadi 3 (Selesai/Siap Disajikan)
            $order->order_status_id = 3;
        }

        // 3. Simpan perubahan ke database
        $order->save();

        // 4. Kembalikan koki ke halaman dapur sebelumnya
        return redirect()->back()->with('success', 'Status pesanan berhasil diupdate!');
    }
}