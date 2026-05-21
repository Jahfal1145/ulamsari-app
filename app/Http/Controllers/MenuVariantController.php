<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuVariantController extends Controller
{
    public function index($id)
    {
        $variants = DB::table('menu_variants')
            ->where('menu_id', $id)
            ->get();

        // ★ HARUS RETURN LANGSUNG ARRAY! Jangan dibungkus objek macem-macem
        // Biar Javascript di pelanggan bisa langsung melakukan looping (forEach)
        return response()->json($variants);
    }

    public function store(Request $request, $id)
    {
        try {
            $options = is_array($request->options) ? $request->options : [$request->options];

            DB::table('menu_variants')->insert([
                'menu_id'        => $id,
                'variant_name'   => $request->variant_name,
                'options'        => json_encode($options),
                'default_option' => $request->default_option,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);

            // ★ JEBAKAN BATMAN DIHAPUS. Kembalikan response sukses normal!
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => '🚨 CRASH SQL: ' . $e->getMessage()
            ], 500); // Tambahkan kode error 500 biar frontend tau ini error
        }
    }

    public function update(Request $request, $id)
{
    try {

        $options = $request->options;

        // Paksa jadi array bersih
        if (!is_array($options)) {
            $options = [$options];
        }

        $options = array_values(array_filter($options));

        DB::table('menu_variants')
            ->where('id', $id)
            ->update([
                'variant_name'   => $request->variant_name,
                'options'        => json_encode($options),
                'default_option' => $options[0] ?? null,
                'updated_at'     => now(),
            ]);

        return response()->json([
            'success' => true
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'error' => $e->getMessage()
        ], 500);

    }
}

    public function destroy($id)
    {
        try {
            DB::table('menu_variants')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => '🚨 Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}