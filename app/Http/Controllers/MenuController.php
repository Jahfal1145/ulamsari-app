<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')
                    ->select('menus.*', 'categories.name as category_name')
                    ->orderBy('menus.id', 'desc')
                    ->get();

        $categories = DB::table('categories')->get();
        return view('admin.menu.index', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return back()->with('success', 'Menu ' . $request->name . ' berhasil ditambahkan!');
    }

    public function toggleActive($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $menu->is_active = !$menu->is_active;
            $menu->save();
            $status = $menu->is_active ? 'Tersedia' : 'Habis';
            return back()->with('success', 'Status menu ' . $menu->name . ' diubah menjadi ' . $status);
        }
        return back()->with('error', 'Menu tidak ditemukan.');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return back()->with('error', 'Menu tidak ditemukan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $menu->image = $request->file('image')->store('menus', 'public');
        }

        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->category_id = $request->category_id;
        $menu->description = $request->description;
        $menu->save();

        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return back()->with('error', 'Menu tidak ditemukan.');
        }

        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus!');
    }
}