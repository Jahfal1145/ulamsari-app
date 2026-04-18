<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // Pastikan model Menu sudah di-import

class PelangganController extends Controller
{
    // NAH, PASTIKAN ADA FUNGSI INI DI DALAM CLASS:
    public function index($meja)
    {
        // Ambil data menu dari database MySQL
        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')
                    ->select('menus.*', 'categories.name as category_name')
                    ->where('is_active', true)
                    ->get();

        // Oper variabel $menus dan $meja ke view
        return view('pelanggan.index', compact('menus', 'meja'));
    }

    // Nanti di sini buat fungsi store() untuk simpan pesanan pelanggan
    public function store(Request $request) 
    {
        // kodenya nanti...
    }
}