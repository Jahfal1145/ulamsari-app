<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\Menu;

use Illuminate\Support\Facades\DB;



class MenuController extends Controller

{

    public function index()

    {

        // Ambil semua menu beserta nama kategorinya

        $menus = Menu::join('categories', 'menus.category_id', '=', 'categories.id')

                    ->select('menus.*', 'categories.name as category_name')

                    ->orderBy('menus.id', 'desc')

                    ->get();



        // Ambil daftar kategori untuk pilihan di form tambah menu

        $categories = DB::table('categories')->get();



        return view('admin.menu.index', compact('menus', 'categories'));

    }



    public function store(Request $request)

    {

        $request->validate([

            'name' => 'required|string|max:255',

            'price' => 'required|numeric|min:0',

            'category_id' => 'required|integer',

        ]);



        Menu::create([

            'name' => $request->name,

            'price' => $request->price,

            'category_id' => $request->category_id,

            'is_active' => true, // Default menu baru langsung aktif

        ]);



        return back()->with('success', 'Menu ' . $request->name . ' berhasil ditambahkan!');

    }



    // Fungsi untuk mematikan/menyalakan menu (Habis/Tersedia)

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


    // FUNGSI BARU UNTUK UPDATE/EDIT MENU
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek jika ada gambar baru yang diupload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            // Simpan gambar baru
            $menu->image = $request->file('image')->store('menus', 'public');
        }

        // Update data lainnya
        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->category_id = $request->category_id;
        $menu->save();

        return back()->with('success', 'Data menu ' . $menu->name . ' berhasil diperbarui!');
    }
}