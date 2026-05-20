<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 font-sans text-gray-800 p-6 relative min-h-screen">

{{-- HEADER --}}
<div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <div>
        <h1 class="text-3xl font-black text-orange-600 uppercase tracking-tight">Kelola Data Menu</h1>
        <p class="text-sm text-gray-500 mt-1">Manajemen menu makanan, harga, deskripsi & varian pilihan</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('kasir.index') }}" class="bg-black text-white font-bold px-6 py-2 rounded-xl text-sm hover:bg-gray-800 transition">
            KE HALAMAN KASIR
        </a>
        <form action="{{ route('pin.logout', 'admin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2.5 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
            </button>
        </form>
    </div>
</div>

{{-- NOTIFIKASI --}}
@if(session('success'))
    <div id="alertMsg" class="bg-green-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
    </div>
    <script>setTimeout(() => document.getElementById('alertMsg')?.remove(), 3500);</script>
@endif
@if(session('error'))
    <div id="alertError" class="bg-red-500 text-white p-4 rounded-xl font-bold mb-6 shadow-md flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        {{ session('error') }}
    </div>
    <script>setTimeout(() => document.getElementById('alertError')?.remove(), 4500);</script>
@endif

{{-- MAIN GRID --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- KOLOM KIRI: FORM TAMBAH MENU --}}
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
                       class="w-full px-2 py-2 rounded-xl border border-gray-300 text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Deskripsi Menu (Opsional)</label>
                <textarea name="description" rows="3" placeholder="Contoh: Ayam bakar dengan bumbu kecap manis..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-orange-500 font-semibold text-sm"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md text-sm">
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
                        <th class="p-4 w-52 text-center">Aksi</th>
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

                        {{-- DETAIL --}}
                        <td class="p-4">
                            <div class="font-bold text-gray-900 text-base">{{ $menu->name }}</div>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="bg-orange-50 text-orange-600 font-bold text-xs px-2.5 py-0.5 rounded-md border border-orange-100">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <span class="bg-gray-100 text-gray-600 font-semibold text-xs px-2.5 py-0.5 rounded-md">{{ $menu->category_name }}</span>
                                {{-- Badge jumlah varian --}}
                                <span id="variant-badge-{{ $menu->id }}" class="bg-purple-100 text-purple-700 font-bold text-xs px-2.5 py-0.5 rounded-md hidden">
                                    <span id="variant-count-{{ $menu->id }}">0</span> Varian
                                </span>
                            </div>
                            @if($menu->description)
                                <p class="text-gray-400 text-xs mt-1.5 italic line-clamp-1">"{{ $menu->description }}"</p>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.menu.toggleActive', $menu->id) }}"
                               class="inline-block px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider transition-colors {{ $menu->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                {{ $menu->is_active ? 'Tersedia' : 'Habis' }}
                            </a>
                        </td>

                        {{-- AKSI --}}
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                {{-- ★ TOMBOL VARIAN --}}
                                <button onclick="openVariantModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                                        class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-xl font-bold text-xs transition shadow-sm flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                    Varian
                                </button>
                                <button onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->category_id }}, '{{ addslashes($menu->description) }}')"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-xl font-bold text-xs transition shadow-sm">
                                    Edit
                                </button>
                                <a href="{{ route('admin.menu.destroy', $menu->id) }}" onclick="return confirm('Hapus menu {{ $menu->name }}?')"
                                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-xl font-bold text-xs transition shadow-sm">
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

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- ★ MODAL KELOLA VARIAN (GOOGLE FORM STYLE)              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="variantModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="bg-purple-600 p-5 text-white flex justify-between items-center rounded-t-2xl shrink-0">
            <div>
                <h3 class="text-lg font-black uppercase tracking-wide">Kelola Varian Menu</h3>
                <p id="variantModalSubtitle" class="text-purple-200 text-sm font-semibold mt-0.5"></p>
            </div>
            <button onclick="closeVariantModal()" class="text-white hover:text-purple-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
            {{-- Kiri: Form tambah varian baru --}}
            <div class="md:w-80 shrink-0 p-5 border-r border-gray-100 overflow-y-auto">
                <h4 class="font-black text-gray-700 uppercase text-xs tracking-wider mb-4">Tambah Varian Baru</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Nama Varian</label>
                        <input type="text" id="new_variant_name" placeholder="cth: Level Pedas..."
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:border-purple-500 font-semibold text-sm">
                    </div>

                    {{-- UI ALA GOOGLE FORM --}}
                    <div>
                        <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Daftar Opsi</label>
                        <div id="new_options_container" class="space-y-2 mb-2">
                            </div>
                        <button type="button" onclick="addOptionRow('new_options_container')" class="text-purple-600 text-xs font-black flex items-center gap-1 hover:text-purple-800 transition bg-purple-50 px-3 py-1.5 rounded-lg w-full justify-center">
                            + Tambah Opsi
                        </button>
                    </div>

                    <button onclick="saveNewVariant()"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-black py-3 rounded-xl text-sm uppercase transition shadow-md">
                        + Simpan Varian
                    </button>
                </div>
            </div>

            {{-- Kanan: Daftar varian yang sudah ada --}}
            <div class="flex-1 p-5 overflow-y-auto bg-gray-50/50">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-black text-gray-700 uppercase text-xs tracking-wider">Varian Terdaftar</h4>
                    <span id="variant-list-count" class="text-xs font-bold text-gray-400"></span>
                </div>
                <div id="variant-loading" class="flex flex-col items-center justify-center py-12 text-gray-300">
                    <svg class="w-8 h-8 animate-spin mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <p class="text-sm font-bold">Memuat varian...</p>
                </div>
                <div id="variant-empty" class="hidden flex-col items-center justify-center py-12 text-gray-300">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <p class="text-sm font-bold">Belum ada varian</p>
                </div>
                <div id="variant-list" class="hidden space-y-3"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- ★ MODAL EDIT VARIAN                                    --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="editVariantModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-gray-100">
        <div class="bg-blue-600 p-5 text-white flex justify-between items-center rounded-t-2xl">
            <h3 class="text-lg font-black uppercase">Edit Varian</h3>
            <button onclick="closeEditVariantModal()" class="text-white hover:text-blue-200 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="edit_variant_id">
            <div>
                <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Nama Varian</label>
                <input type="text" id="edit_variant_name" class="w-full px-3 py-2.5 rounded-xl border border-blue-400 focus:outline-none focus:border-blue-600 font-semibold text-sm">
            </div>
            
            {{-- UI ALA GOOGLE FORM (EDIT) --}}
            <div>
                <label class="block text-gray-600 font-bold text-xs mb-1.5 uppercase">Daftar Opsi</label>
                <div id="edit_options_container" class="space-y-2 mb-2"></div>
                <button type="button" onclick="addOptionRow('edit_options_container')" class="text-blue-600 text-xs font-black flex items-center gap-1 hover:text-blue-800 transition bg-blue-50 px-3 py-1.5 rounded-lg w-full justify-center">
                    + Tambah Opsi
                </button>
            </div>

                <button onclick="closeEditVariantModal()" class="flex-1 py-3 border-2 border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition text-sm">Batal</button>
                <button onclick="saveEditVariant()" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition text-sm">Update Data</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT MENU (Original) --}}
<div id="editMenuModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden">
        <div class="bg-blue-600 p-6 text-white flex justify-between items-center">
            <h3 class="text-xl font-black uppercase tracking-wide">Edit Data Menu</h3>
            <button onclick="closeEditModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editMenuForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Nama Menu</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Harga (Rp)</label>
                <input type="number" name="price" id="edit_price" required class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm">
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
                <input type="file" name="image" accept="image/*" class="w-full px-2 py-2 rounded-xl border border-blue-500 text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Deskripsi (Opsional)</label>
                <textarea name="description" id="edit_description" rows="3" class="w-full px-4 py-3 rounded-xl border border-blue-500 focus:outline-none focus:border-blue-700 font-semibold text-sm"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl uppercase tracking-widest transition shadow-md text-sm">Update Data Menu</button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT                                             --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let activeMenuId = null;

    async function apiFetch(url, method = 'GET', body = null) {
        // Trik Anti-Cache: Tambahin timestamp acak di akhir URL biar browser gak bisa nga-kalin
        const finalUrl = method === 'GET' ? `${url}?t=${new Date().getTime()}` : url;
        
        const opts = {
            method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            cache: 'no-store' // Paksa browser mutlak minta data baru ke server
        };
        if (body) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        const res = await fetch(finalUrl, opts);
        return res.json();
    }

    function showToast(msg, type = 'success') {
        const el = document.createElement('div');

        el.className = `
            fixed bottom-6 right-6 z-[9999]
            px-5 py-3 rounded-xl
            font-bold text-white text-sm
            shadow-2xl transition-all
            ${type === 'error'
                ? 'bg-red-600'
                : 'bg-green-600'}
        `;

        el.innerText = msg;

        document.body.appendChild(el);

        setTimeout(() => {
            el.classList.add('opacity-0', 'translate-y-2');

            setTimeout(() => el.remove(), 300);
        }, 2500);
    }

    // FUNGSI BUAT NAMBAH KOLOM INPUT ALA GOOGLE FORM
    function addOptionRow(containerId, value = '') {
        const container = document.getElementById(containerId);
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        
        // Pilihan warna border berdasarkan modal (Tambah = Ungu, Edit = Biru)
        const borderColor = containerId === 'new_options_container' ? 'border-gray-300 focus:border-purple-500' : 'border-blue-400 focus:border-blue-600';
        
        row.innerHTML = `
            <input type="text" value="${value}" placeholder="Opsi..." class="w-full px-3 py-2 rounded-xl border ${borderColor} focus:outline-none font-semibold text-sm option-input">
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus baris">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        `;
        container.appendChild(row);
    }

    // AMBIL DATA DARI SEMUA KOLOM INPUT
    function getOptionsArray(containerId) {
        const inputs = document.querySelectorAll(`#${containerId} .option-input`);
        return Array.from(inputs).map(input => input.value.trim()).filter(val => val !== '');
    }

    function openVariantModal(menuId, menuName) {
    activeMenuId = menuId;

    // buka modal
    const modal = document.getElementById('variantModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // subtitle
    document.getElementById('variantModalSubtitle').innerText =
        `Menu: ${menuName}`;

    // reset form
    document.getElementById('new_variant_name').value = '';

    const container = document.getElementById('new_options_container');
    container.innerHTML = '';

    // kasih 1 row default
    addOptionRow('new_options_container');

    // load variants
    loadVariants(menuId);
}

    function closeVariantModal() {
        document.getElementById('variantModal').classList.replace('flex', 'hidden');
        activeMenuId = null;
    }

    async function loadVariants(menuId) {
        document.getElementById('variant-loading').classList.remove('hidden');
        document.getElementById('variant-loading').classList.add('flex');
        document.getElementById('variant-empty').classList.add('hidden');
        document.getElementById('variant-list').classList.add('hidden');

        try {
            const res = await apiFetch(`/admin/menu/${menuId}/variants`);
                
            renderVariantList(res, menuId);
        } catch (e) {
            showToast('Gagal memuat varian! Cek Inspect -> Console', 'error');
            console.error("ERROR DETEKTIF:", e);
        }
    }

    function renderVariantList(resData, menuId) {
        // Ekstrak data array-nya
        const variants = Array.isArray(resData) ? resData : (resData.data || []);

        document.getElementById('variant-loading').classList.add('hidden');
        document.getElementById('variant-loading').classList.remove('flex');

        const countEl = document.getElementById('variant-list-count');
        const badgeEl = document.getElementById(`variant-badge-${menuId}`);
        const badgeCount = document.getElementById(`variant-count-${menuId}`);

        if (!variants || variants.length === 0) {
            document.getElementById('variant-empty').classList.remove('hidden');
            document.getElementById('variant-empty').classList.add('flex');
            countEl.innerText = '0 varian';
            if (badgeEl) badgeEl.classList.add('hidden');
            return;
        }

        if (badgeEl && badgeCount) {
            badgeCount.innerText = variants.length;
            badgeEl.classList.remove('hidden');
        }

        countEl.innerText = `${variants.length} varian`;
        const list = document.getElementById('variant-list');
        list.classList.remove('hidden');
        list.innerHTML = '';

        variants.forEach(v => {
            // PELINDUNG ANTI-CRASH: Cek bentuk data sebelum di-parse
            let optionsArr = [];
            if (Array.isArray(v.options)) {
                optionsArr = v.options;
            } else if (typeof v.options === 'string') {
                try {
                    optionsArr = JSON.parse(v.options);
                } catch (e) {
                    // Kalau data kotor (bukan JSON), pecah manual pakai koma biar gak error
                    optionsArr = v.options.split(',').map(s => s.trim());
                }
            }

            const pills = optionsArr.map(opt => {
                const isDefault = opt === v.default_option;
                return `<span class="inline-block px-2 py-0.5 rounded-lg text-xs font-bold ${isDefault ? 'bg-purple-500 text-white' : 'bg-white border border-gray-200 text-gray-600'}">${opt}${isDefault ? ' ★' : ''}</span>`;
            }).join('');

            // Amankan teks sebelum masuk ke fungsi onClick HTML
            const safeName = escapeJs(v.variant_name || '');
            const safeOptions = escapeJs(JSON.stringify(optionsArr));
            const safeDefault = escapeJs(v.default_option || '');

            list.insertAdjacentHTML('beforeend', `
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm hover:border-purple-300 transition mb-3" id="variant-card-${v.id}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-black text-gray-800 text-sm">${v.variant_name}</p>
                            <p class="text-gray-400 text-xs font-semibold mt-0.5">${optionsArr.length} pilihan</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openEditVariantModal(${v.id}, '${safeName}', '${safeOptions}')"
                                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg font-bold text-xs transition">
                                Edit
                            </button>
                            {{-- ★ INI TOMBOL HAPUSNYA UDAH STANDBY ★ --}}
                            <button onclick="deleteVariant(${v.id})"
                                    class="bg-red-50 hover:bg-red-500 text-red-600 hover:text-white px-3 py-1.5 rounded-lg font-bold text-xs transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1.5">${pills}</div>
                </div>
            `);
        });
    }

    function escapeJs(str) {
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    async function saveNewVariant() {
    const name = document.getElementById('new_variant_name').value.trim();
    const options = getOptionsArray('new_options_container');

    if (!name) {
        showToast('Nama varian wajib diisi', 'error');
        return;
    }

    if (options.length === 0) {
        showToast('Minimal isi 1 opsi varian', 'error');
        return;
    }

    try {

        // AUTO DEFAULT = OPSI PERTAMA
        const defaultOption = options[0];

        const result = await apiFetch(`/admin/menu/${activeMenuId}/variants`, 'POST', {
            variant_name: name,
            options: options,
            default_option: defaultOption,
        });

        if (result.error) {
            showToast(result.error, 'error');
            return;
        }

        showToast('✅ Varian berhasil ditambahkan!', 'success');

        // RESET FORM
        document.getElementById('new_variant_name').value = '';
        document.getElementById('new_options_container').innerHTML = '';

        addOptionRow('new_options_container');

        // RELOAD VARIANTS
        await loadVariants(activeMenuId);

    } catch (e) {
        showToast('Gagal menyimpan varian', 'error');
        console.error(e);
    }
}

    function openEditVariantModal(id, name, optionsJson) {
        // Parse kembali array yang tadinya di-escape
        const optionsArr = JSON.parse(optionsJson.replace(/&quot;/g, '"')); 

        document.getElementById('edit_variant_id').value = id;
        document.getElementById('edit_variant_name').value = name;
        
        // Render baris opsi satu per satu
        const container = document.getElementById('edit_options_container');
        container.innerHTML = '';
        optionsArr.forEach(opt => addOptionRow('edit_options_container', opt));

        document.getElementById('editVariantModal').classList.replace('hidden', 'flex');
    }

    function closeEditVariantModal() {
        document.getElementById('editVariantModal').classList.replace('flex', 'hidden');
    }

    async function saveEditVariant() {
        const id = document.getElementById('edit_variant_id').value;
        const name = document.getElementById('edit_variant_name').value.trim();
        const options = getOptionsArray('edit_options_container');
        const def = options[0] || '';

        if (!name) { showToast('Nama varian wajib diisi', 'error'); return; }
        if (options.length === 0) { showToast('Minimal isi 1 opsi varian', 'error'); return; }

        try {
            const result = await apiFetch(`/admin/menu/variants/${id}/update`, 'POST', {
                variant_name: name,
                options: options,
                default_option: def,
            });

            if (result.error) { showToast(result.error, 'error'); return; }

            showToast('Varian berhasil diupdate!');
            closeEditVariantModal();
            await loadVariants(activeMenuId);
        } catch (e) {
            showToast('Gagal mengupdate varian', 'error');
        }
    }

    async function deleteVariant(variantId) {
        if (!confirm('Hapus varian ini?')) return;
        try {
            await apiFetch(`/admin/menu/variants/${variantId}/delete`);
            showToast('Varian dihapus');
            await loadVariants(activeMenuId);
        } catch (e) {
            showToast('Gagal menghapus varian', 'error');
        }
    }

    function openEditModal(id, name, price, category_id, description) {
        document.getElementById('editMenuForm').action = `/admin/menu/update/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_category').value = category_id;
        document.getElementById('edit_description').value = description;
        document.getElementById('editMenuModal').classList.replace('hidden', 'flex');
    }

    function closeEditModal() {
        document.getElementById('editMenuModal').classList.replace('flex', 'hidden');
    }
</script>
</body>
</html>