<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800 p-8 relative">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-orange-600 uppercase">Kelola Data Menu</h1>
            <a href="{{ route('kasir.index') }}" class="bg-black text-white px-6 py-2 rounded-xl font-bold hover:bg-gray-800 transition shadow-lg">Ke Halaman Kasir</a>
        </div>

        @if(session('success'))
            <div id="alertMsg" class="bg-green-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md transition-all">{{ session('success') }}</div>
            <script>setTimeout(() => document.getElementById('alertMsg').remove(), 3000);</script>
        @endif
        @if(session('error'))
            <div id="alertError" class="bg-red-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md transition-all">{{ session('error') }}</div>
            <script>setTimeout(() => document.getElementById('alertError').remove(), 3000);</script>
        @endif

        <div class="flex gap-8 items-start">
            {{-- FORM TAMBAH MENU (KIRI) --}}
            <div class="w-1/3 bg-white p-6 rounded-3xl shadow-xl border-t-4 border-orange-500 sticky top-8">
                <h2 class="text-xl font-bold mb-6 uppercase border-b pb-2">Tambah Menu Baru</h2>
                <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Foto Menu</label>
                        <input type="file" name="image" accept="image/png, image/jpeg, image/jpg"
                               class="w-full p-2 border-2 border-gray-200 rounded-xl outline-none focus:border-orange-500 font-semibold file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Nama Menu</label>
                        <input type="text" name="name" required placeholder="Contoh: Ayam Bakar Madu" 
                               class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-orange-500 font-semibold">
                    </div>
                    <div class="mb-4">
                        <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Harga (Rp)</label>
                        <input type="number" name="price" required placeholder="Contoh: 25000" 
                               class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-orange-500 font-semibold">
                    </div>
                    <div class="mb-6">
                        <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Kategori</label>
                        <select name="category_id" required class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-orange-500 font-semibold">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md">
                        Simpan Menu
                    </button>
                </form>
            </div>

            {{-- DAFTAR MENU (KANAN) --}}
            <div class="w-2/3 bg-white p-6 rounded-3xl shadow-xl">
                <h2 class="text-xl font-bold mb-6 uppercase border-b pb-2">Daftar Menu Saat Ini</h2>
                <div class="overflow-y-auto max-h-[600px] pr-2">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-sm uppercase sticky top-0 z-10">
                            <tr>
                                <th class="p-4 rounded-tl-xl">Foto</th>
                                <th class="p-4">Nama Menu</th>
                                <th class="p-4">Harga</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 rounded-tr-xl text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                            <tr class="border-b hover:bg-orange-50 transition">
                                <td class="p-4">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-16 h-16 object-cover rounded-xl shadow-sm border border-gray-200">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-xl flex items-center justify-center text-[10px] text-gray-400 font-bold text-center">NO IMG</div>
                                    @endif
                                </td>
                                <td class="p-4 font-bold">
                                    {{ $menu->name }}<br>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $menu->category_name }}</span>
                                </td>
                                <td class="p-4 text-orange-600 font-black whitespace-nowrap">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('admin.menu.toggle', $menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-1 text-[10px] font-bold rounded-full uppercase transition
                                            {{ $menu->is_active ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700' : 'bg-red-100 text-red-700 hover:bg-green-100 hover:text-green-700' }}">
                                            {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- TOMBOL EDIT --}}
                                        <button type="button" onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->category_id }})" class="bg-blue-100 text-blue-600 p-2 rounded-xl hover:bg-blue-500 hover:text-white transition shadow-sm" title="Edit Menu">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        
                                        {{-- TOMBOL HAPUS --}}
                                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu {{ addslashes($menu->name) }} secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-100 text-red-600 p-2 rounded-xl hover:bg-red-500 hover:text-white transition shadow-sm" title="Hapus Menu">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL EDIT MENU ===== --}}
    <div id="editMenuModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8">
            <div class="flex justify-between items-center mb-6 border-b pb-2">
                <h2 class="text-2xl font-bold uppercase text-gray-800">Edit Menu</h2>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 p-1 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>
                </button>
            </div>

            <form id="editMenuForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Ganti Foto <span class="text-xs text-gray-400 normal-case">(Opsional)</span></label>
                    <input type="file" name="image" accept="image/png, image/jpeg, image/jpg"
                           class="w-full p-2 border-2 border-gray-200 rounded-xl outline-none focus:border-blue-500 font-semibold file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                <div class="mb-4">
                    <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Nama Menu</label>
                    <input type="text" name="name" id="edit_name" required 
                           class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-blue-500 font-semibold">
                </div>
                <div class="mb-4">
                    <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" required 
                           class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-blue-500 font-semibold">
                </div>
                <div class="mb-6">
                    <label class="block font-bold text-sm text-gray-600 mb-2 uppercase">Kategori</label>
                    <select name="category_id" id="edit_category" required class="w-full p-3 border-2 border-gray-200 rounded-xl outline-none focus:border-blue-500 font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md">
                    Update Menu
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, price, category_id) {
            // Set action URL form agar mengarah ke ID yang benar
            document.getElementById('editMenuForm').action = `/admin/menu/update/${id}`;
            
            // Isi form dengan data saat ini
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category_id;
            
            // Tampilkan modal
            document.getElementById('editMenuModal').classList.remove('hidden');
            document.getElementById('editMenuModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editMenuModal').classList.add('hidden');
            document.getElementById('editMenuModal').classList.remove('flex');
        }
    </script>
</body>
</html>