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

        // JURUS ANTI WHERE-IN: Pakai where dan orWhere agar tidak ada error kurung siku lagi
        $pendingOrders = Order::with(['orderItems.menu'])
                        ->where(function($q) {
                            $q->where('order_status_id', 1)
                              ->orWhere('order_status_id', 4);
                        })
                        ->get()
                        ->groupBy('table_id'); 

        $historyOrders = Order::with(['orderItems.menu'])
                        ->where('order_status_id', '!=', 4) 
                        ->orderBy('id', 'desc')
                        ->limit(30)
                        ->get();

        return view('kasir.index', compact('menus', 'tables', 'pendingOrders', 'historyOrders'));
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

        $paymentMethod = $request->payment_method ?? 'Belum Bayar';

        DB::beginTransaction();
        try {
            $order = Order::create([
                'table_id' => $request->table_id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_price' => collect($cart)->sum('subtotal'),
                'order_status_id' => 1,
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

            $successMsg = ($paymentMethod !== 'Belum Bayar') 
                ? 'Pesanan Meja ' . $request->table_id . ' dikirim. LUNAS (' . $paymentMethod . ')' 
                : 'Pesanan Meja ' . $request->table_id . ' dikirim. (BAYAR NANTI)';

            return back()->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $query = Order::with(['orderItems.menu'])
                    ->where('payment_method', '!=', 'Belum Bayar');

        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
            $judulFile = $start_date . '_sd_' . $end_date;
        } else {
            $query->whereDate('created_at', now()->format('Y-m-d'));
            $judulFile = 'Hari_Ini';
        }

        $orders = $query->orderBy('id', 'desc')->get();
        $fileName = 'Laporan_UlamSari_' . $judulFile . '.xls';

        // Variabel penampung total untuk laporan
        $grandTotalItem = 0;
        $grandTotalUang = 0;

        $html = '<table border="1" style="border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: red; color: white; font-weight: bold;">';
        $html .= '<th>NO</th><th>Nomer Pesanan</th><th>Tanggal</th><th>Nama Pembeli</th><th>No HP</th><th style="min-width:300px;">Pesanan</th><th>No Meja / Takeaway</th><th>Total Item</th><th>Total Harga</th><th>Metode Pembayaran</th>';
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach ($orders as $order) {
            $lokasi = $order->table_id == '0' ? 'Takeaway' : $order->table_id;
            $pesananArr = [];
            $totalItemPerOrder = $order->orderItems->sum('quantity');
            
            foreach ($order->orderItems as $item) {
                $namaMenu = $item->menu ? $item->menu->name : 'Item'; 
                $pesananArr[] = $namaMenu . ':' . $item->quantity;
            }
            
            // Tambahkan ke grand total
            $grandTotalItem += $totalItemPerOrder;
            $grandTotalUang += $order->total_price;

            $html .= '<tr>';
            $html .= '<td>' . $no++ . '</td>';
            $html .= '<td>' . $order->order_number . '</td>';
            $html .= '<td>' . $order->created_at->format('d/m/Y') . '</td>';
            $html .= '<td>' . ($order->customer_name ?? '-') . '</td>';
            $html .= '<td style="mso-number-format:\'\@\'">' . ($order->phone_number ?? '-') . '</td>';
            $html .= '<td>' . implode(', ', $pesananArr) . '</td>';
            $html .= '<td>' . $lokasi . '</td>';
            $html .= '<td>' . $totalItemPerOrder . '</td>';
            $html .= '<td style="background-color: yellow;">Rp. ' . number_format($order->total_price, 0, ',', '.') . '</td>';
            $html .= '<td>' . $order->payment_method . '</td>';
            $html .= '</tr>';
        }

        // PENAMBAHAN BARIS TOTAL DI PALING BAWAH
        $html .= '<tr style="background-color: #f2f2f2; font-weight: bold;">';
        $html .= '<td colspan="7" style="text-align: right; padding-right: 10px;">GRAND TOTAL:</td>';
        $html .= '<td style="background-color: #90ee90;">' . $grandTotalItem . ' Item</td>'; // Total Item
        $html .= '<td style="background-color: #90ee90;">Rp. ' . number_format($grandTotalUang, 0, ',', '.') . '</td>'; // Total Uang
        $html .= '<td></td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';

        return response($html, 200, [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName"
        ]);
    }

    public function konfirmasi($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        $order->order_status_id = 1; 
        
        if ($order->payment_method == 'Belum Bayar' || $order->payment_method == 'Tunai') {
            $order->payment_method = 'Tunai';
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Pembayaran Lunas! Pesanan otomatis masuk ke Dapur.');
    }

    // FUNGSI UNTUK DIAM-DIAM DIAMBIL OLEH JAVASCRIPT (AJAX) - VERSI ANTI ERROR
    public function apiPendingOrders()
    {
        $pendingOrders = Order::with(['orderItems.menu'])
                        ->where(function($q) {
                            $q->where('order_status_id', 1)
                              ->orWhere('order_status_id', 4); // Status 4 = Pelanggan dari HP
                        })
                        ->get()
                        ->groupBy('table_id'); 
                        
        return response()->json($pendingOrders);
    }

    public function getNota($id)
    {
        $order = Order::with('orderItems.menu')->findOrFail($id);
        return response()->json([
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'table_id'       => $order->table_id,
            'customer_name'  => $order->customer_name ?? 'Tanpa Nama', // PENAMBAHAN DATA
            'phone_number'   => $order->phone_number ?? '-', // PENAMBAHAN DATA
            'payment_method' => $order->payment_method,
            'total_price'    => $order->total_price,
            'created_at'     => $order->created_at,
            'order_items'    => $order->orderItems->map(fn($item) => [
                'name'     => $item->menu->name ?? $item->name,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ])
        ]);
    }
}