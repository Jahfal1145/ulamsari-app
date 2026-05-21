<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .modal-enter { animation: modalIn 0.2s ease-out forwards; }
        @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 99px; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 p-6 relative min-h-screen">

{{-- ═══════════ HEADER ═══════════ --}}
<div class="flex justify-between items-center bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <div>
        <h1 class="text-2xl font-black text-orange-600 uppercase tracking-tight">Kelola Data Menu</h1>
        <p class="text-xs text-gray-400 mt-0.5 font-semibold">Manajemen menu, kategori & varian pilihan · Ulam Sari</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- ★ Searchbar --}}
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="menuSearchInput" onkeyup="filterMenuTable()" placeholder="Cari menu..."
                   class="pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-500 font-semibold text-sm transition w-52">
        </div>
        {{-- ★ Tombol Kelola Kategori --}}
        <button onclick="openCategoryModal()"
            class="flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Kategori
        </button>
        <a href="{{ route('kasir.index') }}" class="bg-black text-white font-bold px-5 py-2.5 rounded-xl text-sm hover:bg-gray-800 transition shadow-sm">
            Ke Kasir
        </a>
        <form action="{{ route('pin.logout', 'admin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-xl transition shadow-sm" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                </svg>
            </button>
        </form>
    </div>
</div>

{{-- NOTIFIKASI --}}
@if(session('success'))
    <div id="alertMsg" class="bg-green-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    <script>setTimeout(() => document.getElementById('alertMsg')?.remove(), 3500);</script>
@endif
@if(session('error'))
    <div id="alertError" class="bg-red-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    <script>setTimeout(() => document.getElementById('alertError')?.remove(), 4500);</script>
@endif

{{-- ═══════════ MAIN GRID ═══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- KOLOM KIRI: FORM TAMBAH MENU --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-fit sticky top-6">
        <h2 class="text-base font-black text-gray-900 uppercase tracking-wide mb-5 pb-3 border-b border-gray-100">Tambah Menu Baru</h2>
        <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Nama Menu</label>
                <input type="text" name="name" required placeholder="Masukkan nama masakan..."
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-500 font-semibold text-sm transition">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Harga (Rp)</label>
                <input type="number" name="price" required placeholder="Contoh: 25000"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-500 font-semibold text-sm transition">
            </div>
            <div>
    <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">
        Kategori
    </label>

    <div class="space-y-2 border border-gray-200 rounded-xl p-4 max-h-48 overflow-y-auto">
        @foreach($categories as $cat)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox"
                       name="categories[]"
                       value="{{ $cat->id }}"
                       class="w-4 h-4 text-orange-500 rounded border-gray-300 focus:ring-orange-500">

                <span class="font-semibold text-sm text-gray-700">
                    {{ $cat->name }}
                </span>
            </label>
        @endforeach
    </div>
</div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Foto (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-2 py-2 rounded-xl border border-gray-200 text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 transition">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Deskripsi (Opsional)</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat menu..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-orange-500 font-semibold text-sm resize-none transition"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3.5 rounded-xl uppercase tracking-wider transition shadow-md text-sm">
                Simpan Menu Baru
            </button>
        </form>
    </div>

    {{-- KOLOM KANAN: TABEL --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-base font-black text-gray-900 uppercase tracking-wide">Daftar Menu</h2>
            <span class="text-xs font-bold text-gray-400">{{ count($menus) }} menu terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="menuTable">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-400 font-bold text-[11px] uppercase tracking-wider">
                        <th class="p-4 text-center w-20">Foto</th>
                        <th class="p-4">Detail Menu</th>
                        <th class="p-4 w-28 text-center">Status</th>
                        <th class="p-4 w-44 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50/60 transition-colors group menu-row">
                        <td class="p-4 text-center">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="Menu" class="w-14 h-14 object-cover rounded-xl border border-gray-100 shadow-sm mx-auto group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-14 h-14 bg-gray-100 text-gray-400 rounded-xl flex items-center justify-center font-black text-[9px] uppercase border mx-auto">No Img</div>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-900 menu-name">{{ $menu->name }}</div>
                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                <span class="bg-orange-50 text-orange-600 font-bold text-[11px] px-2 py-0.5 rounded-md border border-orange-100">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                                <span class="bg-gray-100 text-gray-600 font-semibold text-[11px] px-2 py-0.5 rounded-md">
                                    {{ $menu->categories->pluck('name')->join(', ') }}
                                </span>
                                {{-- ★ Badge varian — diisi JS saat halaman load --}}
                                <span id="variant-badge-{{ $menu->id }}"
                                      class="bg-purple-100 text-purple-700 font-bold text-[11px] px-2 py-0.5 rounded-md hidden items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 110 20A10 10 0 0112 2zm0 2a8 8 0 100 16A8 8 0 0012 4z"/></svg>
                                    <span id="variant-count-{{ $menu->id }}">0</span> Varian
                                </span>
                            </div>
                            @if($menu->description)
                                <p class="text-gray-400 text-[11px] mt-1 italic line-clamp-1">"{{ $menu->description }}"</p>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.menu.toggleActive', $menu->id) }}"
                               class="inline-block px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wide transition-colors {{ $menu->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                            </a>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openVariantModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                                        class="bg-purple-50 hover:bg-purple-500 text-purple-600 hover:text-white px-3 py-2 rounded-xl font-bold text-[11px] transition flex items-center gap-1 border border-purple-200 hover:border-purple-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    Varian
                                </button>
                                <button onclick='openEditModal( {{ $menu->id }}, @json($menu->categories->pluck("id")), "{{ addslashes($menu->name) }}", {{ $menu->price }}, "{{ addslashes($menu->description) }}")'
                                        class="bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white px-3 py-2 rounded-xl font-bold text-[11px] transition border border-blue-200 hover:border-blue-500">
                                    Edit
                                </button>
                                <a href="{{ route('admin.menu.destroy', $menu->id) }}"
                                   onclick="return confirm('Hapus menu {{ addslashes($menu->name) }}?')"
                                   class="bg-red-50 hover:bg-red-500 text-red-600 hover:text-white px-3 py-2 rounded-xl font-bold text-[11px] transition border border-red-200 hover:border-red-500">
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-16 text-center text-gray-300 font-bold">Belum ada menu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- ★ MODAL KELOLA VARIAN                                          --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="variantModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] modal-enter">

        {{-- Header --}}
        <div class="bg-purple-600 px-6 py-4 text-white flex justify-between items-center rounded-t-2xl shrink-0">
            <div>
                <h3 class="text-base font-black uppercase tracking-wide">Kelola Varian Menu</h3>
                <p id="variantModalSubtitle" class="text-purple-200 text-xs font-semibold mt-0.5"></p>
            </div>
            <button onclick="closeVariantModal()" class="text-purple-200 hover:text-white transition p-1 rounded-lg hover:bg-purple-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden">

            {{-- Panel Kiri: Form Tambah --}}
            <div class="w-72 shrink-0 p-5 border-r border-gray-100 overflow-y-auto scrollbar-thin bg-gray-50/50">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Tambah Varian Baru</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 font-bold text-xs mb-1.5">Nama Varian</label>
                        <input type="text" id="new_variant_name" placeholder="cth: Level Pedas..."
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 font-semibold text-sm transition">
                    </div>
                    <div>
                        <label class="block text-gray-600 font-bold text-xs mb-1.5">Daftar Opsi</label>
                        <div id="new_options_container" class="space-y-2 mb-2"></div>
                        <button type="button" onclick="addOptionRow('new_options_container', 'purple')"
                                class="w-full text-purple-600 text-xs font-black bg-purple-50 hover:bg-purple-100 px-3 py-2 rounded-xl transition flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Tambah Opsi
                        </button>
                    </div>

                    <button onclick="saveNewVariant()"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-black py-3 rounded-xl text-sm uppercase tracking-wide transition shadow-sm">
                        Simpan Varian
                    </button>
                </div>

                <div class="mt-5 bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-3">Contoh</p>
                    <div class="space-y-2.5 text-xs text-gray-600">
                        <div><p class="font-bold text-gray-700">Level Pedas</p><p class="text-gray-400">Tidak Pedas, Sedang, Pedas</p></div>
                        <div><p class="font-bold text-gray-700">Level Es</p><p class="text-gray-400">Normal, Less Ice, Tanpa Es</p></div>
                    </div>
                </div>
            </div>

            {{-- Panel Kanan: Daftar Varian --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Varian Terdaftar</p>
                    <span id="variant-list-count" class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full"></span>
                </div>

                {{-- Loading --}}
                <div id="variant-loading" class="flex-1 flex flex-col items-center justify-center text-gray-300 p-8">
                    <svg class="w-7 h-7 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-sm font-bold">Memuat...</p>
                </div>

                {{-- Empty --}}
                <div id="variant-empty" class="hidden flex-1 flex-col items-center justify-center text-gray-300 p-8">
                    <svg class="w-14 h-14 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <p class="text-sm font-bold">Belum ada varian</p>
                    <p class="text-xs mt-1 text-gray-400">Tambah varian di panel kiri</p>
                </div>

                {{-- List --}}
                <div id="variant-list" class="hidden flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- ★ MODAL EDIT VARIAN                                            --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="editVariantModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl modal-enter">
        <div class="bg-blue-600 px-6 py-4 text-white flex justify-between items-center rounded-t-2xl">
            <h3 class="text-base font-black uppercase">Edit Varian</h3>
            <button onclick="closeEditVariantModal()" class="text-blue-200 hover:text-white p-1 rounded-lg hover:bg-blue-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <input type="hidden" id="edit_variant_id">
            <div>
                <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Nama Varian</label>
                <input type="text" id="edit_variant_name"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 font-semibold text-sm transition">
            </div>
            <div>
                <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Daftar Opsi</label>
                <div id="edit_options_container" class="space-y-2 mb-2"></div>
                <button type="button" onclick="addOptionRow('edit_options_container', 'blue')"
                        class="w-full text-blue-600 text-xs font-black bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Opsi
                </button>
            </div>
            <div class="flex gap-3 pt-2">
                <button onclick="closeEditVariantModal()"
                        class="flex-1 py-3 px-4 border-2 border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition text-sm">
                    Batal
                </button>
                <button onclick="saveEditVariant()"
                        class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition text-sm shadow-sm">
                    Update Varian
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- ★ MODAL KELOLA KATEGORI                                        --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="categoryModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[88vh] modal-enter">

        {{-- Header --}}
        <div class="bg-indigo-600 px-6 py-4 text-white flex justify-between items-center rounded-t-2xl shrink-0">
            <div>
                <h3 class="text-base font-black uppercase tracking-wide">Kelola Kategori Menu</h3>
                <p class="text-indigo-200 text-xs font-semibold mt-0.5">Tambah, edit, dan hapus kategori menu</p>
            </div>
            <button onclick="closeCategoryModal()" class="text-indigo-200 hover:text-white transition p-1 rounded-lg hover:bg-indigo-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden">
            {{-- Panel Kiri: Form Tambah --}}
            <div class="w-72 shrink-0 p-5 border-r border-gray-100 overflow-y-auto scrollbar-thin bg-gray-50/50">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Tambah Kategori Baru</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 font-bold text-xs mb-1.5">Nama Kategori</label>
                        <input type="text" id="new_category_name" placeholder="cth: Makanan Berat..."
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 font-semibold text-sm transition"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();saveNewCategory();}">
                    </div>
                    <button onclick="saveNewCategory()"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 rounded-xl text-sm uppercase tracking-wide transition shadow-sm">
                        Simpan Kategori
                    </button>
                </div>
            </div>

            {{-- Panel Kanan: Daftar Kategori --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori Terdaftar</p>
                    <span id="category-list-count" class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full"></span>
                </div>

                {{-- Loading --}}
                <div id="category-loading" class="flex-1 flex flex-col items-center justify-center text-gray-300 p-8">
                    <svg class="w-7 h-7 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-sm font-bold">Memuat...</p>
                </div>

                {{-- Empty --}}
                <div id="category-empty" class="hidden flex-1 flex-col items-center justify-center text-gray-300 p-8">
                    <svg class="w-14 h-14 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <p class="text-sm font-bold">Belum ada kategori</p>
                </div>

                {{-- List --}}
                <div id="category-list" class="hidden flex-1 overflow-y-auto p-4 space-y-2.5 scrollbar-thin"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- ★ MODAL EDIT KATEGORI                                          --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="editCategoryModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl modal-enter">
        <div class="bg-indigo-600 px-6 py-4 text-white flex justify-between items-center rounded-t-2xl">
            <h3 class="text-base font-black uppercase">Edit Kategori</h3>
            <button onclick="closeEditCategoryModal()" class="text-indigo-200 hover:text-white p-1 rounded-lg hover:bg-indigo-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <input type="hidden" id="edit_category_id">
            <div>
                <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Nama Kategori</label>
                <input type="text" id="edit_category_name_input"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 font-semibold text-sm transition"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();saveEditCategory();}">
            </div>
            <div class="flex gap-3">
                <button onclick="closeEditCategoryModal()"
                        class="flex-1 py-3 px-4 border-2 border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition text-sm">
                    Batal
                </button>
                <button onclick="saveEditCategory()"
                        class="flex-1 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition text-sm shadow-sm">
                    Update Kategori
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL EDIT MENU                                                --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="editMenuModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl modal-enter overflow-hidden max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="bg-blue-600 px-6 py-4 text-white flex justify-between items-center sticky top-0">
            <h3 class="text-base font-black uppercase">Edit Menu</h3>
            <button onclick="closeEditModal()" class="text-blue-200 hover:text-white p-1 rounded-lg hover:bg-blue-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editMenuForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Nama Menu</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 font-semibold text-sm transition">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Harga (Rp)</label>
                <input type="number" name="price" id="edit_price" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 font-semibold text-sm transition">
            </div>
            <div>
    <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">
        Kategori
    </label>

    <div class="space-y-2 border border-gray-200 rounded-xl p-4 max-h-48 overflow-y-auto">
        @foreach($categories as $cat)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox"
                       name="categories[]"
                       value="{{ $cat->id }}"
                       class="edit-category-checkbox w-4 h-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500">

                <span class="font-semibold text-sm text-gray-700">
                    {{ $cat->name }}
                </span>
            </label>
        @endforeach
    </div>
</div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Ganti Foto (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-2 py-2 rounded-xl border border-gray-200 text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1.5 text-xs uppercase">Deskripsi (Opsional)</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-blue-500 font-semibold text-sm resize-none transition"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3.5 rounded-xl uppercase tracking-wide transition shadow-sm text-sm">
                Update Menu
            </button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let activeMenuId = null;

// ── Searchbar / Filter Tabel ──────────────────────────────────────
function filterMenuTable() {
    const input = document.getElementById('menuSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#menuTable tbody tr.menu-row');
    
    rows.forEach(row => {
        const menuNameElement = row.querySelector('.menu-name');
        if (menuNameElement) {
            const menuName = menuNameElement.innerText.toLowerCase();
            row.style.display = menuName.includes(input) ? '' : 'none';
        }
    });
}

// ── Helpers ──────────────────────────────────────────────────────
async function apiFetch(url, method = 'GET', body = null) {

    const opts = {
        method,
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        }
    };

    if (body) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }

    const res = await fetch(url, opts);

    let data = {};

    try {
        data = await res.json();
    } catch (e) {}

    if (!res.ok) {
        throw {
            status: res.status,
            data
        };
    }

    return data;
}

function showToast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl font-bold text-white text-sm shadow-2xl transition-all ${type === 'error' ? 'bg-red-600' : type === 'warning' ? 'bg-yellow-500' : 'bg-green-600'}`;
    el.innerText = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(8px)'; setTimeout(() => el.remove(), 300); }, 2500);
}

// ── Option row UI Diperbaiki ───────────────────────────────────────
function addOptionRow(containerId, color = 'purple', value = '') {
    const container = document.getElementById(containerId);
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2 group';
    
    const focusRing = color === 'blue' ? 'focus:border-blue-500 focus:ring-4 focus:ring-blue-100' : 'focus:border-purple-500 focus:ring-4 focus:ring-purple-100';
    const btnHover = color === 'blue' ? 'hover:bg-blue-50 hover:text-blue-600' : 'hover:bg-red-50 hover:text-red-600';
    
    row.innerHTML = `
        <div class="flex-1 relative">
            <input type="text" value="${value}" placeholder="Nama opsi..."
                   class="w-full px-3 py-2.5 rounded-xl border border-gray-300 ${focusRing} outline-none font-semibold text-sm option-input transition">
        </div>
        <button type="button" onclick="this.closest('.group').remove()"
                class="p-2.5 bg-gray-100 text-gray-400 rounded-xl ${btnHover} transition shrink-0 shadow-sm" title="Hapus Opsi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>`;
    
    container.appendChild(row);
    row.querySelector('input').focus();
}

function getOptionsArray(containerId) {
    return Array.from(document.querySelectorAll(`#${containerId} .option-input`))
        .map(i => i.value.trim()).filter(Boolean);
}

// ════════════════════════════════════════════════════════════════
// VARIAN MODAL
// ════════════════════════════════════════════════════════════════
async function openVariantModal(menuId, menuName) {
    activeMenuId = menuId;
    document.getElementById('variantModalSubtitle').innerText = menuName;
    document.getElementById('variantModal').classList.replace('hidden', 'flex');

    // Reset form
    document.getElementById('new_variant_name').value = '';
    const container = document.getElementById('new_options_container');
    container.innerHTML = '';
    addOptionRow('new_options_container', 'purple');

    await loadVariants(menuId);
}

function closeVariantModal() {
    document.getElementById('variantModal').classList.replace('flex', 'hidden');
    activeMenuId = null;
}

async function loadVariants(menuId) {
    document.getElementById('variant-loading').classList.replace('hidden', 'flex');
    document.getElementById('variant-empty').classList.add('hidden');
    document.getElementById('variant-list').classList.add('hidden');

    try {
        const data = await apiFetch(`/admin/menu/${menuId}/variants`);
        renderVariantList(Array.isArray(data) ? data : (data.data || []), menuId);
    } catch (e) {
        showToast('Gagal memuat varian', 'error');
        document.getElementById('variant-loading').classList.replace('flex', 'hidden');
    }
}

function renderVariantList(variants, menuId) {
    document.getElementById('variant-loading').classList.replace('flex', 'hidden');

    // Update badge di tabel — tanpa perlu buka modal dulu
    const badgeEl    = document.getElementById(`variant-badge-${menuId}`);
    const countEl    = document.getElementById(`variant-count-${menuId}`);
    const listCountEl = document.getElementById('variant-list-count');

    if (badgeEl && countEl) {
        if (variants.length > 0) {
            countEl.innerText = variants.length;
            badgeEl.classList.remove('hidden');
            badgeEl.classList.add('inline-flex');
        } else {
            badgeEl.classList.add('hidden');
            badgeEl.classList.remove('inline-flex');
        }
    }

    if (listCountEl) listCountEl.innerText = `${variants.length} varian`;

    if (variants.length === 0) {
        document.getElementById('variant-empty').classList.remove('hidden');
        document.getElementById('variant-empty').classList.add('flex');
        return;
    }

    const list = document.getElementById('variant-list');
    list.classList.remove('hidden');
    list.innerHTML = '';

    variants.forEach(v => {
        let optsArr = [];
        if (Array.isArray(v.options)) optsArr = v.options;
        else { try { optsArr = JSON.parse(v.options); } catch { optsArr = String(v.options).split(',').map(s => s.trim()); } }

        const pills = optsArr.map(opt =>
            `<span class="inline-block px-2 py-0.5 rounded-lg text-[11px] font-bold ${opt === v.default_option ? 'bg-purple-500 text-white' : 'bg-white border border-gray-200 text-gray-600'}">${opt}${opt === v.default_option ? ' ★' : ''}</span>`
        ).join('');

        list.insertAdjacentHTML('beforeend', `
            <div class="bg-white border border-gray-200 rounded-2xl p-4 hover:border-purple-300 hover:shadow-sm transition" id="variant-card-${v.id}">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-black text-gray-800 text-sm">${v.variant_name}</p>
                        <p class="text-gray-400 text-[11px] font-semibold mt-0.5">${optsArr.length} pilihan · Default: <span class="text-purple-600">${v.default_option || optsArr || '-'}</span></p>
                    </div>
                    <div class="flex gap-2 shrink-0 ml-3">
                        <button onclick='openEditVariantModal(
    ${v.id},
    ${JSON.stringify(v.variant_name)},
    ${JSON.stringify(optsArr)}
)'
                                class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-[11px] transition">Edit</button>
                        <button onclick="deleteVariant(${v.id})"
                                class="bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-3 py-1.5 rounded-lg font-bold text-[11px] transition">Hapus</button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5">${pills}</div>
            </div>`);
    });
}

async function saveNewVariant() {
    const name    = document.getElementById('new_variant_name').value.trim();
    const options = getOptionsArray('new_options_container');

    if (!name)           { showToast('Nama varian wajib diisi', 'error'); return; }
    if (!options.length) { showToast('Minimal 1 opsi diperlukan', 'error'); return; }

    try {
        await apiFetch(`/admin/menu/${activeMenuId}/variants`, 'POST', {
            variant_name: name,
            options,
            default_option: options[0]
        });

        // Reset form
        document.getElementById('new_variant_name').value = '';
        document.getElementById('new_options_container').innerHTML = '';
        addOptionRow('new_options_container', 'purple');

        // ★ Auto-refresh daftar
        await loadVariants(activeMenuId);
    } catch (e) {
        showToast(e.data?.error || 'Gagal menyimpan varian', 'error');
    }
}

// Edit Varian
function openEditVariantModal(id, name, optsArr) {

    document.getElementById('edit_variant_id').value = id;
    document.getElementById('edit_variant_name').value = name;

    const c = document.getElementById('edit_options_container');
    c.innerHTML = '';

    optsArr.forEach(opt => {
        addOptionRow('edit_options_container', 'blue', opt);
    });

    document
        .getElementById('editVariantModal')
        .classList.replace('hidden', 'flex');
}

function closeEditVariantModal() {
    document.getElementById('editVariantModal').classList.replace('flex', 'hidden');
}

async function saveEditVariant() {
    const id      = document.getElementById('edit_variant_id').value;
    const name    = document.getElementById('edit_variant_name').value.trim();
    const options = getOptionsArray('edit_options_container');

    if (!name)           { showToast('Nama varian wajib diisi', 'error'); return; }
    if (!options.length) { showToast('Minimal 1 opsi diperlukan', 'error'); return; }

    try {
        await apiFetch(`/admin/menu/variants/${id}/update`, 'POST', {
            variant_name: name, options, default_option: options[0]
        });
        showToast('Varian berhasil diupdate!');
        closeEditVariantModal();
        await loadVariants(activeMenuId);
    } catch (e) {
    console.log(e);

    alert(
        JSON.stringify(e.data ?? e, null, 2)
    );

    showToast(
        e.data?.error ||
        e.message ||
        'Gagal mengupdate varian',
        'error'
    );
}
}

async function deleteVariant(variantId) {
    if (!confirm('Hapus varian ini?')) return;
    try {
        await apiFetch(`/admin/menu/variants/${variantId}/delete`, 'DELETE');
        showToast('Varian dihapus');
        await loadVariants(activeMenuId);
    } catch (e) {
        showToast('Gagal menghapus', 'error');
    }
}

// ════════════════════════════════════════════════════════════════
// KATEGORI MODAL
// ════════════════════════════════════════════════════════════════
async function openCategoryModal() {
    document.getElementById('categoryModal').classList.replace('hidden', 'flex');
    await loadCategories();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.replace('flex', 'hidden');
}

async function loadCategories() {
    document.getElementById('category-loading').classList.replace('hidden', 'flex');
    document.getElementById('category-empty').classList.add('hidden');
    document.getElementById('category-list').classList.add('hidden');

    try {
        const data = await apiFetch('/admin/categories');
        renderCategoryList(Array.isArray(data) ? data : (data.data || []));
    } catch (e) {
        showToast('Gagal memuat kategori. Pastikan Route API sudah dibuat!', 'error');
        document.getElementById('category-loading').classList.replace('flex', 'hidden');
    }
}

function renderCategoryList(categories) {
    document.getElementById('category-loading').classList.replace('flex', 'hidden');
    document.getElementById('category-list-count').innerText = `${categories.length} kategori`;

    if (categories.length === 0) {
        document.getElementById('category-empty').classList.remove('hidden');
        document.getElementById('category-empty').classList.add('flex');
        return;
    }

    const list = document.getElementById('category-list');
    list.classList.remove('hidden');
    list.innerHTML = '';

    categories.forEach(cat => {
        list.insertAdjacentHTML('beforeend', `
            <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3.5 hover:border-indigo-300 hover:shadow-sm transition flex justify-between items-center" id="cat-card-${cat.id}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <span class="font-bold text-gray-800 text-sm">${cat.name}</span>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button onclick='openEditVariantModal(
    ${v.id},
    ${JSON.stringify(v.variant_name)},
    ${JSON.stringify(optsArr)}
)'
                            class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-[11px] transition">Edit</button>
                    <button onclick="deleteCategory(${cat.id})"
                            class="bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-3 py-1.5 rounded-lg font-bold text-[11px] transition">Hapus</button>
                </div>
            </div>`);
    });
}

async function saveNewCategory() {
    const name = document.getElementById('new_category_name').value.trim();
    if (!name) { showToast('Nama kategori wajib diisi', 'error'); return; }

    try {
        await apiFetch('/admin/categories', 'POST', { name });
        showToast('Kategori berhasil ditambahkan!');
        document.getElementById('new_category_name').value = '';
        await loadCategories();
    } catch (e) {
        showToast(e.data?.error || e.data?.message || 'Kategori sudah ada / gagal disimpan', 'error');
    }
}

function openEditCategoryModal(id, name) {
    document.getElementById('edit_category_id').value = id;
    document.getElementById('edit_category_name_input').value = name;
    document.getElementById('editCategoryModal').classList.replace('hidden', 'flex');
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').classList.replace('flex', 'hidden');
}

async function saveEditCategory() {
    const id   = document.getElementById('edit_category_id').value;
    const name = document.getElementById('edit_category_name_input').value.trim();
    if (!name) { showToast('Nama kategori wajib diisi', 'error'); return; }

    try {
        await apiFetch(`/admin/categories/${id}/update`, 'POST', { name });
        showToast('Kategori berhasil diupdate!');
        closeEditCategoryModal();
        await loadCategories();
    } catch (e) {
        showToast(e.data?.error || e.data?.message || 'Gagal update kategori', 'error');
    }
}

async function deleteCategory(id) {
    if (!confirm('Hapus kategori ini?')) return;
    try {
        await apiFetch(`/admin/categories/${id}/delete`);
        showToast('Kategori dihapus');
        await loadCategories();
    } catch (e) {
        showToast(e.data?.error || 'Gagal menghapus kategori', 'error');
    }
}

// ════════════════════════════════════════════════════════════════
// EDIT MENU MODAL
// ════════════════════════════════════════════════════════════════
function openEditModal(id, categoryIds, name, price, description) {

    document.getElementById('editMenuForm').action =
        `/admin/menu/update/${id}`;

    document.getElementById('edit_name').value = name;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_description').value = description;

    document.querySelectorAll('.edit-category-checkbox')
        .forEach(cb => cb.checked = false);

    categoryIds.forEach(id => {

        const checkbox = document.querySelector(
            `.edit-category-checkbox[value="${id}"]`
        );

        if (checkbox) {
            checkbox.checked = true;
        }
    });

    document.getElementById('editMenuModal')
        .classList.replace('hidden', 'flex');
}

function closeEditModal() {
    document.getElementById('editMenuModal').classList.replace('flex', 'hidden');
}

// ════════════════════════════════════════════════════════════════
// ★ LOAD SEMUA BADGE VARIAN SAAT HALAMAN PERTAMA DIBUKA
// ════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {
    const badges = document.querySelectorAll('[id^="variant-badge-"]');
    for (const badge of badges) {
        const menuId = badge.id.replace('variant-badge-', '');
        try {
            const data = await apiFetch(`/admin/menu/${menuId}/variants`);
            const variants = Array.isArray(data) ? data : (data.data || []);
            const countEl = document.getElementById(`variant-count-${menuId}`);
            if (variants.length > 0 && countEl) {
                countEl.innerText = variants.length;
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            }
        } catch {
            // skip menu yang gagal — tidak blokir yang lain
        }
    }
});
</script>
</body>
</html>