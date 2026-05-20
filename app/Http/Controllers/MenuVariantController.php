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

    return response()->json([
        'id_yang_diklik'   => $id,
        'total_semua_di_db' => DB::table('menu_variants')->count(),
        'total_yang_cocok' => $variants->count(),
        'data' => $variants
    ]);
}
    public function store(Request $request, $id)
    {
        try {
            $options = is_array($request->options) ? $request->options : [$request->options];

            // Coba maksa simpan data ke database
            $insertId = DB::table('menu_variants')->insertGetId([
                'menu_id'        => $id,
                'variant_name'   => $request->variant_name,
                'options'        => json_encode($options),
                'default_option' => $request->default_option,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);

            // 🔥 JEBAKAN BATMAN: Kita sengaja ngelempar 'error' padahal sukses!
            // Biar notif merah muncul dan ngasih tau ID-nya ke layar kamu.
            return response()->json([
                'error' => '✅ ALARM!! BERHASIL SIMPAN! ID di Database: ' . $insertId
            ]);

        } catch (\Exception $e) {
            // Kalau SQL nya nge-crash gara-gara tipe data/tabel, pesannya langsung dikirim ke layar!
            return response()->json([
                'error' => '🚨 CRASH SQL: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $options = is_array($request->options) ? $request->options : [$request->options];
            DB::table('menu_variants')->where('id', $id)->update([
                'variant_name'   => $request->variant_name,
                'options'        => json_encode($options),
                'default_option' => $request->default_option,
                'updated_at'     => Carbon::now(),
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => '🚨 CRASH SQL: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::table('menu_variants')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}