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

        // Ambil pesanan yang masih Pending (1)
        $pendingOrders = Order::with(['orderItems.menu'])
                        ->where('order_status_id', 1)
                        ->get()
                        ->groupBy('table_id'); 

        // AMBIL DATA RIWAYAT (Patokannya: payment_method BUKAN 'Belum Bayar')
        $historyOrders = Order::with(['orderItems.menu'])
                        ->where('payment_method', '!=', 'Belum Bayar')
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
    // FUNGSI UNTUK EXPORT RIWAYAT KE EXCEL (CSV)
// FUNGSI UNTUK EXPORT RIWAYAT KE EXCEL DENGAN STYLE (HTML to XLS)
    // FUNGSI UNTUK EXPORT RIWAYAT KE EXCEL DENGAN STYLE (HTML to XLS) + FILTER
    public function export(Request $request)
    {
        // 1. Tangkap pilihan dari dropdown, default-nya Harian
        $filter = $request->query('filter', 'hari_ini');
        $judulFile = 'Harian';

        // 2. Mulai query dasar (yang sudah lunas)
        $query = Order::with(['orderItems.menu'])
                      ->where('payment_method', '!=', 'Belum Bayar');

        // 3. Modifikasi query berdasarkan filter
        if ($filter == 'hari_ini') {
            $query->whereDate('created_at', now()->format('Y-m-d'));
            $judulFile = 'Harian';
        } elseif ($filter == 'minggu_ini') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $judulFile = 'Mingguan';
        } elseif ($filter == 'bulan_ini') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            $judulFile = 'Bulanan';
        }

        // Eksekusi query
        $orders = $query->orderBy('id', 'desc')->get();

        // Nama file dinamis sesuai filter
        $fileName = 'Riwayat_Transaksi_' . $judulFile . '_UlamSari_' . date('Y-m-d') . '.xls';

        // Kita buat kerangka tabel HTML dengan CSS Inline
        $html = '<table border="1" style="border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">';
        
        // --- HEADER TABEL (Warna Merah, Teks Putih) ---
        $html .= '<thead>';
        $html .= '<tr style="background-color: red; color: white; font-weight: bold;">';
        $html .= '<th style="padding: 5px;">NO</th>';
        $html .= '<th style="padding: 5px;">Nomer Pesanan</th>';
        $html .= '<th style="padding: 5px;">Tanggal</th>';
        $html .= '<th style="padding: 5px; min-width: 300px;">Pesanan</th>';
        $html .= '<th style="padding: 5px;">No Meja / Takeaway</th>';
        $html .= '<th style="padding: 5px;">Total Item</th>';
        $html .= '<th style="padding: 5px;">Total Harga</th>';
        $html .= '<th style="padding: 5px;">Metode Pembayaran</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        
        // --- ISI TABEL ---
        $html .= '<tbody>';
        $no = 1;
        foreach ($orders as $order) {
            $lokasi = $order->table_id == '0' ? 'Takeaway' : $order->table_id;
            $totalItem = $order->orderItems->sum('quantity');

            // Menggabungkan nama menu dan jumlahnya
            $pesananArr = [];
            foreach ($order->orderItems as $item) {
                $namaMenu = $item->menu ? $item->menu->name : 'Item'; 
                $pesananArr[] = $namaMenu . ':' . $item->quantity;
            }
            $pesananStr = implode(', ', $pesananArr); 

            $html .= '<tr>';
            $html .= '<td>' . $no++ . '</td>';
            $html .= '<td>' . $order->order_number . '</td>';
            $html .= '<td>' . $order->created_at->format('d/m/Y') . '</td>';
            $html .= '<td>' . $pesananStr . '</td>';
            $html .= '<td>' . $lokasi . '</td>';
            $html .= '<td>' . $totalItem . '</td>';
            $html .= '<td style="background-color: yellow;">Rp. ' . number_format($order->total_price, 0, ',', '.') . '</td>';
            $html .= '<td>' . $order->payment_method . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $headers = array(
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        return response($html, 200, $headers);
    }
}