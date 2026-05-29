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
        $menus = Menu::with(['variants', 'categories'])
                    ->where('is_active', true)
                    ->get();
        
        $tables = Table::all();
        $categories = DB::table('categories')->get();

        $pendingOrders = Order::with(['orderItems.menu'])
                        ->where(function($q) {
                            $q->where('order_status_id', 1)
                              ->orWhere('order_status_id', 2)
                              ->orWhere('order_status_id', 4);
                        })
                        ->get()
                        ->groupBy('table_id'); 

        $historyOrders = Order::with(['orderItems.menu'])
                        ->where('order_status_id', 3)
                        ->whereDate('created_at', now()->toDateString())
                        ->orderBy('id', 'desc')
                        ->get();

        return view('kasir.index', compact('menus', 'tables', 'pendingOrders', 'historyOrders', 'categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'cart_data'     => 'required',
            'table_id'      => 'required',
            'customer_name' => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
        ]);

        $cart = json_decode($request->cart_data, true);

        if (!$cart || count($cart) == 0) {
            return back()->with('error', 'Pilih menu dulu rek!');
        }

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $order = Order::create([
                'table_id'        => $request->table_id,
                'order_number'    => 'ORD-' . strtoupper(uniqid()),
                'total_price'     => collect($cart)->sum('subtotal'),
                'order_status_id' => 1,
                'payment_method'  => $request->payment_method ?? 'Tunai',
                'customer_name'   => strtoupper($request->customer_name),
                'phone_number'    => $request->phone_number ?? '-',
            ]);

            foreach ($cart as $item) {
                $notes = $item['notes'] ?? '-';
                
                if ($request->table_id == '0') {
                    if (stripos($notes, 'Bungkus') === false && stripos($notes, 'Takeaway') === false) {
                        $notes = ($notes === '-' || $notes === '') ? 'Bungkus' : $notes . ' (Bungkus)';
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['menu_id'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'notes'    => $notes,
                ]);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();
            
            $msg = $request->table_id == '0' 
                ? 'Pesanan TAKEAWAY berhasil diproses.' 
                : 'Pesanan Meja ' . $request->table_id . ' berhasil diproses.';

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollback();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function konfirmasi($id)
    {
        $order = Order::findOrFail($id);
        $order->order_status_id = 1;
        $order->save();
        return redirect()->back()->with('success', 'Pesanan dikonfirmasi dan dikirim ke dapur!');
    }

    public function export(Request $request)
    {
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');

        $query = Order::with(['orderItems.menu'])
                      ->where('order_status_id', 3);

        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        } else {
            $query->whereDate('created_at', now()->toDateString());
        }

        $orders = $query->orderBy('id', 'desc')->get();

        // 1. SIAPKAN VARIABEL UNTUK MENAMPUNG TOTAL
        $grandTotalHarga = 0;
        $grandTotalItem  = 0;

        $html  = '<table border="1" style="border-collapse: collapse; text-align: center; font-family: Arial, sans-serif;">';
        $html .= '<thead><tr style="background-color: #2563eb; color: white;">';
        $html .= '<th>NO</th><th>Nomer Pesanan</th><th>Tanggal</th><th>Nama Pembeli</th><th>No HP</th><th>Pesanan</th><th>Total Harga</th><th>Metode</th>';
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach ($orders as $order) {
            $pesananArr = [];
            $totalItemPerOrder = 0; // Hitung item per nota ini

            foreach ($order->orderItems as $item) {
                $pesananArr[] = ($item->menu->name ?? 'Item') . ' (x' . $item->quantity . ')';
                $totalItemPerOrder += $item->quantity; // Tambah jumlah item pesanan
            }
            
            // 2. AKUMULASIKAN KE GRAND TOTAL
            $grandTotalHarga += $order->total_price;
            $grandTotalItem  += $totalItemPerOrder;
            
            $html .= '<tr>';
            $html .= '<td>' . $no++ . '</td>';
            $html .= '<td>' . $order->order_number . '</td>';
            // Menambahkan setTimezone agar jam & tanggal terkonversi ke WIB saat diexport
            $html .= '<td>' . $order->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . '</td>';
            $html .= '<td>' . ($order->customer_name ?? '-') . '</td>';
            $html .= '<td style="mso-number-format:\'\@\'">' . ($order->phone_number ?? '-') . '</td>';
            $html .= '<td>' . implode(', ', $pesananArr) . '</td>';
            // REVISI: Menambahkan format titik ribuan pada harga per baris order
            $html .= '<td style="text-align: right;">' . number_format($order->total_price, 0, ',', '.') . '</td>';
            $html .= '<td>' . $order->payment_method . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>'; 

        // 3. BARIS GRAND TOTAL DI BAWAH TBODY
        $html .= '<tfoot>';
        $html .= '<tr style="font-weight: bold; background-color: #f3f4f6; height: 25px;">';
        $html .= '<td colspan="5" style="text-align: right; padding-right: 10px;">GRAND TOTAL:</td>';
        $html .= '<td style="text-align: left; padding-left: 5px;">' . $grandTotalItem . ' Item</td>'; 
        // REVISI: Menambahkan format titik ribuan pada Grand Total Harga keseluruhan
        $html .= '<td style="text-align: right;">' . number_format($grandTotalHarga, 0, ',', '.') . '</td>'; 
        $html .= '<td>-</td>'; 
        $html .= '</tr>';
        $html .= '</tfoot>';

        $html .= '</table>';

        return response($html, 200, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=Laporan_UlamSari.xls"
        ]);
    }

    public function apiPendingOrders()
    {
        return response()->json(
            Order::with(['orderItems.menu'])
                ->where(function($q) {
                    $q->where('order_status_id', 1)
                      ->orWhere('order_status_id', 2)
                      ->orWhere('order_status_id', 4);
                })
                ->get()
                ->groupBy('table_id')
        );
    }


        public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->orderItems()->delete();
        $order->delete();

        return redirect()->back()->with('success', 'Pesanan berhasil dihapus.');
    }

    public function getNota($id)
    {
        $order = Order::with('orderItems.menu')->findOrFail($id);
        return response()->json([
            'order_number'   => $order->order_number,
            'table_id'       => $order->table_id,
            'customer_name'  => $order->customer_name ?? 'Tanpa Nama',
            'phone_number'   => $order->phone_number ?? '-',
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