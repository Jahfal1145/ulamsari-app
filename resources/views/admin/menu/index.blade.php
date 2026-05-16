<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800 p-6 relative min-h-screen">

    <div class="max-w-7xl mx-auto">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div>
                <h1 class="text-3xl font-black text-orange-600 uppercase tracking-tight">Kelola Data Menu</h1>
                <p class="text-gray-500 text-sm font-medium mt-1">Halaman manajemen menu makanan, harga, dan deskripsi aplikasi Ulam Sari</p>
            </div>
            <a href="{{ route('kasir.index') }}" class="bg-black text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-800 transition shadow-md whitespace-nowrap text-sm uppercase tracking-wider">
                Ke Halaman Kasir
            </a>
        </div>

        {{-- NOTIFIKASI ALERT --}}
        @if(session('success'))
            <div id="alertMsg" class="bg-green-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <script>setTimeout(() => document.getElementById('alertMsg').remove(), 3500);</script>
        @endif

        @if(session('error'))
            <div id="alertError" class="bg-red-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
            <script>setTimeout(() => document.getElementById('alertError').remove(), 4500);</script>
        @endif

        {{-- MAIN LAYOUT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: FORM TAMBAH MENU BARU --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-fit">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-wide mb-6 pb-2 border-b border-gray-100">Tambah Menu Baru</h2>
                
                <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Nama Menu</label>
                        <input type="text" name="name" required placeholder="Masukkan nama masakan..." 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 font-semibold text-sm">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Harga (Rp)</label>
                        <input type="number" name="price" required placeholder="Contoh: 25000" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 font-semibold text-sm">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Kategori</label>
                        <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 font-semibold text-sm bg-white">
                            <option value="" disabled selected>Pilih Kategori Menu</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Foto Menu (Opsional)</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-2 py-2 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2 text-sm">Deskripsi Menu (Opsional)</label>
                        <textarea name="description" rows="3" placeholder="Contoh: Ayam bakar utuh dengan bumbu kecap manis gurih, free sambal bajak..." 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 font-semibold text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md text-sm mt-4">
                        Simpan Menu Baru
                    </button>
                </form>
            </div>

            {{-- KOLOM KANAN: TABEL DAFTAR MENU --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-black text-gray-900 uppercase tracking-wide">Daftar Menu Saat Ini</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold text-xs uppercase tracking-wider">
                                <th class="p-4 text-center w-24">Foto</th>
                                <th class="p-4">Detail Menu</th>
                                <th class="p-4 w-28 text-center">Status</th>
                                <th class="p-4 w-44 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($menus as $menu)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                {{-- FOTO --}}
                                <td class="p-4 text-center">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="Menu" class="w-16 h-16 object-cover rounded-xl border shadow-sm mx-auto">
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center font-black text-[10px] uppercase border mx-auto">No Img</div>
                                    @endif
                                </td>
                                
                                {{-- DETAIL NAMA, HARGA, DESKRIPSI --}}
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 text-base">{{ $menu->name }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="bg-orange-50 text-orange-600 font-bold text-xs px-2.5 py-0.5 rounded-md border border-orange-100">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        <span class="bg-gray-100 text-gray-600 font-semibold text-xs px-2.5 py-0.5 rounded-md">{{ $menu->category_name }}</span>
                                    </div>
                                    @if($menu->description)
                                        <p class="text-gray-400 text-xs mt-1.5 italic line-clamp-1">"{{ $menu->description }}"</p>
                                    @endif
                                </td>

                                {{-- TOGGLE STATUS --}}
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.menu.toggleActive', $menu->id) }}" 
                                       class="inline-block px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider transition-colors {{ $menu->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                        {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                                    </a>
                                </td>

                                {{-- EDIT & HAPUS --}}
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->category_id }}, '{{ addslashes($menu->description) }}')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-xs transition shadow-sm">
                                            Edit
                                        </button>
                                        <a href="{{ route('admin.menu.destroy', $menu->id) }}" onclick="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->name }}?')"
                                           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl font-bold text-xs transition shadow-sm">
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-12 text-center text-gray-400 font-medium">Belum ada menu yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL BOX EDIT MENU (DISEMBUNYIKAN SECARA DEFAULT) --}}
    <div id="editMenuModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden animate-slide-up">
            
            <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
                <h3 class="text-xl font-black uppercase tracking-wide">Edit Data Menu</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="editMenuForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Nama Menu</label>
                    <input type="text" name="name" id="edit_name" required 
                           class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm">
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Harga (Rp)</label>
                    <input type="number" name="price" id="edit_price" required 
                           class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm">
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Kategori</label>
                    <select name="category_id" id="edit_category" required class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Ganti Foto (Opsional)</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-2 py-2 rounded-xl border border-blue-500 focus:outline-none text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Deskripsi Menu (Opsional)</label>
                    <textarea name="description" id="edit_description" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm"></textarea>
                </div>

                {{-- TOMBOL UPDATE SEKARANG SUDAH BENAR BERADA DI DALAM TAG FORM DAN TYPE SUBMIT --}}
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md text-sm mt-4">
                    Update Data Menu
                </button>
            </form>
        </div>
    </div>

    {{-- INTERAKSI JAVASCRIPT MODAL --}}
    <script>
        function openEditModal(id, name, price, category_id, description) {
            // Set URL action form mengarah ke ID menu yang tepat
            document.getElementById('editMenuForm').action = `/admin/menu/update/${id}`;
            
            // Isi nilai input modal dengan data lama yang di-klik
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_category').value = category_id;
            document.getElementById('edit_description').value = description;
            
            // Munculkan Modal dengan mengganti class hidden jadi flex
            document.getElementById('editMenuModal').classList.remove('hidden');
            document.getElementById('editMenuModal').classList.add('flex');
        }

        function closeEditModal() {
            // Sembunyikan kembali Modal
            document.getElementById('editMenuModal').classList.add('hidden');
            document.getElementById('editMenuModal').classList.remove('flex');
        }
    </script>
</body>
</html>