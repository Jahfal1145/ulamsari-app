<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('categories')
            ->orderBy('id', 'desc')
            ->get();

        $categories = Category::all();

        return view('admin.menu.index', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'description' => 'nullable|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath,
            'description' => $request->description,
            'is_active' => 1, 
        ]);

        $menu->categories()->sync($request->categories);

        return back()->with(
            'success',
            'Menu ' . $request->name . ' berhasil ditambahkan!'
        );
    }

    public function toggleActive($id)
    {
        $menu = Menu::find($id);

        if ($menu) {
            $menu->is_active = !$menu->is_active;
            $menu->save();

            $status = $menu->is_active ? 'Tersedia' : 'Habis';

            return back()->with(
                'success',
                'Status menu ' . $menu->name . ' diubah menjadi ' . $status
            );
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
            'categories' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {

            if (
                $menu->image &&
                Storage::disk('public')->exists($menu->image)
            ) {
                Storage::disk('public')->delete($menu->image);
            }

            $menu->image = $request
                ->file('image')
                ->store('menus', 'public');
        }

        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->description = $request->description;

        $menu->save();

        $menu->categories()->sync($request->categories);

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