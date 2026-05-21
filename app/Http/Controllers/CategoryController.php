<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /admin/categories  — list semua (JSON)
    public function index()
    {
        return response()->json(Category::orderBy('name')->get());
    }

    // POST /admin/categories — simpan baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        $cat = Category::create(['name' => $request->name]);
        return response()->json($cat, 201);
    }

    // POST /admin/categories/{id}/update — update
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:100|unique:categories,name,' . $id]);
        $cat = Category::findOrFail($id);
        $cat->update(['name' => $request->name]);
        return response()->json($cat);
    }

    // GET /admin/categories/{id}/delete — hapus
    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        // Cegah hapus kalau masih ada menu yang pakai kategori ini
        if ($cat->menus()->count() > 0) {
            return response()->json([
                'error' => 'Kategori masih digunakan oleh ' . $cat->menus()->count() . ' menu. Hapus atau pindahkan menu tersebut dulu.'
            ], 422);
        }
        $cat->delete();
        return response()->json(['deleted' => true]);
    }
    // Ambil data kategori
    public function apiIndex() {
        $categories = \Illuminate\Support\Facades\DB::table('categories')->get();
        return response()->json($categories);
    }

    // Tambah kategori
    public function apiStore(\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\DB::table('categories')->insert([
            'name' => $request->name
        ]);
        return response()->json(['success' => true]);
    }

    // Update kategori
    public function apiUpdate(\Illuminate\Http\Request $request, $id) {
        \Illuminate\Support\Facades\DB::table('categories')->where('id', $id)->update([
            'name' => $request->name
        ]);
        return response()->json(['success' => true]);
    }

    // Hapus kategori
    public function apiDestroy($id) {
        \Illuminate\Support\Facades\DB::table('categories')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}