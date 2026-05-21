<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pesan - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .slide-up { animation: slideUp 0.3s ease-out forwards; }
        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans antialiased">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div id="alert-success" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-4 py-2 rounded-full font-bold shadow-xl text-sm w-11/12 max-w-sm text-center">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-success')?.remove(), 3000);</script>
    @endif

    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 min-h-screen shadow-2xl relative pb-24 overflow-x-hidden">

        {{-- HEADER --}}
        <div class="bg-white dark:bg-gray-800 sticky top-0 z-10 px-4 pt-6 pb-4 border-b dark:border-gray-700 flex justify-between items-center">
            <div>
                <img src="{{ asset('img/logo_ulam_sari.png') }}" alt="Logo Ulam Sari" class="h-10 w-auto object-contain">
            </div>
            <div class="bg-orange-100 text-orange-600 px-3 py-1 rounded-xl flex flex-col items-center justify-center border border-orange-200">
                <span class="text-[8px] font-bold uppercase">Meja</span>
                <span class="text-xl font-black leading-none">{{ $meja ?? '0' }}</span>
            </div>
        </div>

        {{-- PENCARIAN --}}
        <div class="px-4 mt-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Mau makan apa hari ini?"
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 dark:border-gray-700 dark:bg-gray-900 rounded-2xl focus:border-orange-500 outline-none font-semibold text-sm transition">
            </div>
        </div>

        {{-- KATEGORI (Sekarang Dinamis dari Database!) --}}
        <div class="px-4 mt-6">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition">Semua</button>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <button type="button" onclick="filterMenu('{{ $cat->name }}')" class="filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- DAFTAR MENU --}}
        <div class="px-4 mt-6">
            <div class="grid grid-cols-2 gap-4" id="menuGrid">
                @foreach($menus as $menu)
                <div class="menu-card bg-white dark:bg-gray-900 rounded-2xl border dark:border-gray-700 overflow-hidden shadow-sm flex flex-col h-full"
                    data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">

                    {{-- FOTO MENU --}}
                    <div class="w-full h-48 bg-white dark:bg-gray-800 rounded-t-2xl overflow-hidden">
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105">
                        @else
                            <div class="flex w-full h-full items-center justify-center text-gray-400 font-black text-xs uppercase tracking-widest">No Image</div>
                        @endif
                    </div>

                    {{-- INFORMASI --}}
                    <div class="p-3 flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100 leading-tight mb-1">{{ $menu->name }}</h3>
                            <p class="text-orange-500 font-bold text-xs mb-1">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-[10px] leading-snug line-clamp-2 mb-3 min-h-[28px]">
                                {{ $menu->description ?? 'Hidangan spesial dari Ulam Sari.' }}
                            </p>
                        </div>
                        <button type="button"
                            onclick="openAddModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->category_name) }}')"
                            class="w-full bg-black dark:bg-gray-700 text-white text-xs font-bold py-2 rounded-xl mt-auto hover:bg-orange-500 transition">
                            Tambah
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="h-20"></div>
    </div>

    {{-- FLOATING CART BUTTON --}}
    <div id="floating-cart" class="fixed bottom-4 left-1/2 -translate-x-1/2 w-11/12 max-w-[400px] z-20 hidden transition-all duration-300">
        <button onclick="openCartModal()" class="w-full bg-orange-500 text-white p-4 rounded-2xl flex justify-between items-center shadow-lg">
            <div class="flex items-center gap-3">
                <span id="cart-qty" class="bg-white text-orange-600 font-black w-7 h-7 rounded-full flex items-center justify-center text-xs">0</span>
                <span class="font-bold text-sm">Lihat Pesanan</span>
            </div>
            <span id="cart-total" class="font-black text-lg">Rp 0</span>
        </button>
    </div>

    {{-- ★ MODAL TAMBAH ITEM + VARIAN --}}
    <div id="addModal" class="fixed inset-0 bg-black/60 hidden items-end sm:items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl p-6 slide-up max-h-[85vh] overflow-y-auto scrollbar-hide pb-10">

            {{-- Header modal --}}
            <div class="flex justify-between items-start mb-5 border-b dark:border-gray-700 pb-4">
                <div>
                    <h2 id="modalItemName" class="text-xl font-bold text-gray-800 dark:text-white leading-tight">Nama Item</h2>
                    <p id="modalItemPrice" class="text-orange-500 font-bold text-lg mt-1">Rp 0</p>
                </div>
                <button type="button" onclick="closeAddModal()" class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <input type="hidden" id="modalItemId">

            <div class="space-y-5">
                {{-- ★ TEMPAT VARIAN MUNCUL DARI DATABASE --}}
                <div id="variant-selector-container" class="space-y-4">
                    {{-- Diisi oleh JS saat modal dibuka --}}
                </div>

                {{-- Dine In / Takeaway --}}
                <div class="border-t dark:border-gray-700 pt-4">
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-2">Jenis Pesanan</label>
                    <div class="flex gap-3">
                        <label id="label-dinein" class="flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm">
                            <input type="radio" name="orderType" value="Dine In" class="hidden" checked onchange="toggleTypeUI()">
                            🍽️ Dine In
                        </label>
                        <label id="label-takeaway" class="flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm">
                            <input type="radio" name="orderType" value="Takeaway" class="hidden" onchange="toggleTypeUI()">
                            🛍️ Takeaway
                        </label>
                    </div>
                </div>

                {{-- Jumlah --}}
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-600 dark:text-gray-300 text-sm">Jumlah</span>
                    <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                        <button type="button" onclick="changeQty(-1)" class="w-8 h-8 bg-white dark:bg-gray-800 rounded-lg font-black shadow-sm text-gray-600 dark:text-gray-300">-</button>
                        <input type="number" id="modalQty" value="1" min="1" class="w-8 text-center font-bold bg-transparent outline-none dark:text-white" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-8 h-8 bg-orange-500 text-white rounded-lg font-black shadow-sm">+</button>
                    </div>
                </div>

                {{-- Catatan tambahan --}}
                <div>
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-2">Catatan (Opsional)</label>
                    <input type="text" id="modalNotes" placeholder="cth: Tanpa daun bawang..."
                           class="w-full border-2 border-gray-100 dark:border-gray-700 dark:bg-gray-900 rounded-xl p-3 text-sm font-semibold outline-none focus:border-orange-500 dark:text-white">
                </div>

                <button type="button" onclick="saveToCart()" class="w-full bg-black text-white py-3 rounded-xl font-bold text-sm shadow-lg active:scale-95 transition">
                    Masukkan ke Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL KERANJANG & CHECKOUT --}}
    <div id="cartModal" class="fixed inset-0 bg-black/60 hidden items-end sm:items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md h-[92vh] sm:h-[85vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col slide-up overflow-hidden">

            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800 z-10">
                <div>
                    <h2 class="text-xl font-black text-gray-800 dark:text-white">Keranjang Anda</h2>
                    <p class="text-xs text-orange-500 font-bold">Meja {{ $meja ?? '0' }}</p>
                </div>
                <button type="button" onclick="closeModal('cartModal')" class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pelanggan.store') }}" method="POST" id="checkoutForm" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data_input">
                <input type="hidden" name="table_id" value="{{ $meja ?? '0' }}">

                <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 space-y-6 scrollbar-hide">

                    {{-- DAFTAR PESANAN --}}
                    <div id="cart-container" class="space-y-3"></div>

                    {{-- DATA PEMBELI --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border dark:border-gray-700 shadow-sm space-y-4">
                        <h3 class="font-black text-sm text-gray-800 dark:text-white uppercase border-b dark:border-gray-700 pb-2">Data Pemesan</h3>
                        <div>
                            <label class="block font-bold text-xs text-gray-400 uppercase mb-1">Nama Pembeli <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required placeholder="Masukkan nama..."
                                   class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm outline-none focus:border-orange-500 bg-gray-50 dark:bg-gray-900 dark:text-white uppercase transition-colors">
                        </div>
                        <div>
                            <label class="block font-bold text-xs text-gray-400 uppercase mb-1">No. WhatsApp <span class="text-red-500">*</span></label>
                            <input type="number" name="phone_number" required placeholder="Contoh: 08123456789"
                                   class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm outline-none focus:border-orange-500 bg-gray-50 dark:bg-gray-900 dark:text-white transition-colors">
                        </div>
                    </div>

                    {{-- METODE PEMBAYARAN --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border dark:border-gray-700 shadow-sm mb-4">
                        <h3 class="font-black text-sm text-gray-800 dark:text-white uppercase border-b dark:border-gray-700 pb-2 mb-3">Pilih Pembayaran</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative border-2 border-orange-500 bg-orange-50 dark:bg-orange-900/20 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all" id="label-cash">
                                <input type="radio" name="payment_method" value="Tunai" class="hidden" checked onchange="togglePaymentUI('Tunai')">
                                <svg class="w-6 h-6 text-orange-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                                <span class="text-[10px] font-black uppercase text-center">Bayar Kasir</span>
                            </label>
                            <label class="relative border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all" id="label-winpay">
                                <input type="radio" name="payment_method" value="Winpay" class="hidden" onchange="togglePaymentUI('Winpay')">
                                <svg class="w-6 h-6 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="text-[10px] font-black uppercase text-center">QRIS / Online</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- TOTAL & TOMBOL SUBMIT --}}
                <div class="p-4 border-t dark:border-gray-700 bg-white dark:bg-gray-800 z-10 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-bold text-gray-500 uppercase">Total Pesanan</span>
                        <span id="checkout-total" class="text-2xl font-black text-orange-600">Rp 0</span>
                    </div>
                    <button type="submit" id="submitBtn"
                        class="w-full bg-green-500 hover:bg-green-600 text-white py-3.5 rounded-xl font-black text-sm uppercase tracking-widest shadow-lg active:scale-95 transition">
                        Kirim Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // ── Filter & Search ──────────────────────────────────
        function searchMenu() {
            const val = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(c => {
                c.style.display = c.dataset.name.includes(val) ? 'flex' : 'none';
            });
        }

        function filterMenu(k) {
            document.querySelectorAll('.menu-card').forEach(c => {
                c.style.display = (k === 'semua' || c.dataset.category === k) ? 'flex' : 'none';
            });
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const isActive = (k === 'semua' && btn.innerText.trim().toLowerCase() === 'semua') || btn.innerText.trim() === k;
                btn.className = isActive
                    ? 'filter-btn bg-orange-500 text-white px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition'
                    : 'filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition';
            });
        }

        // ── Dine In / Takeaway UI ────────────────────────────
        function toggleTypeUI() {
            const isDineIn = document.querySelector('input[name="orderType"]:checked').value === 'Dine In';
            document.getElementById('label-dinein').className = isDineIn
                ? 'flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm'
                : 'flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm';
            document.getElementById('label-takeaway').className = !isDineIn
                ? 'flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm'
                : 'flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition text-sm';
        }

        // ── Pembayaran UI ────────────────────────────────────
        function togglePaymentUI(method) {
            const isCash = method === 'Tunai';
            document.getElementById('label-cash').className = isCash
                ? 'relative border-2 border-orange-500 bg-orange-50 dark:bg-orange-900/20 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all'
                : 'relative border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all';
            document.getElementById('label-winpay').className = !isCash
                ? 'relative border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all'
                : 'relative border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl cursor-pointer flex flex-col items-center transition-all';
        }

        // ── Modal helpers ────────────────────────────────────
        function closeModal(id) { document.getElementById(id).classList.replace('flex', 'hidden'); }
        function closeAddModal() { document.getElementById('addModal').classList.replace('flex', 'hidden'); }
        function changeQty(v) {
            const q = document.getElementById('modalQty');
            if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v;
        }

        // ── BUKA MODAL + FETCH VARIAN ────────────────────────
        async function openAddModal(id, name, price, category) {
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;
            document.getElementById('modalQty').value = 1;
            document.getElementById('modalNotes').value = '';

            const dineInRadio = document.querySelector('input[name="orderType"][value="Dine In"]');
            if (dineInRadio) { dineInRadio.checked = true; toggleTypeUI(); }

            document.getElementById('addModal').classList.replace('hidden', 'flex');

            const container = document.getElementById('variant-selector-container');
            container.innerHTML = `<div class="flex items-center gap-2 text-gray-400 text-xs font-semibold py-2">Memuat pilihan varian...</div>`;

            try {
                // Fetch memanggil rute publik (Pastikan route /menu/{id}/variants sudah ada di web.php)
                const res = await fetch(`/menu/${id}/variants?t=${Date.now()}`);
                
                if (!res.ok) throw new Error(`Error server`);

                const variants = await res.json();
                container.innerHTML = '';

                if (!variants || variants.length === 0) return;

                variants.forEach(v => {
                    const opts = Array.isArray(v.options) ? v.options : JSON.parse(v.options || '[]');
                    if (opts.length === 0) return;

                    let optionHTML = opts.map(opt =>
                        `<option value="${opt}" ${opt === v.default_option ? 'selected' : ''}>${opt}</option>`
                    ).join('');

                    container.insertAdjacentHTML('beforeend', `
                        <div class="variant-group" data-variant-name="${escapeHtml(v.variant_name)}">
                            <label class="block font-bold text-xs text-gray-400 uppercase mb-1.5">${escapeHtml(v.variant_name)}</label>
                            <select class="variant-select w-full border-2 border-gray-100 dark:border-gray-700 dark:bg-gray-900 p-3 rounded-xl font-bold text-sm outline-none focus:border-orange-500 dark:text-white bg-gray-50">
                                ${optionHTML}
                            </select>
                        </div>
                    `);
                });
            } catch (e) {
                console.warn('Gagal memuat varian:', e.message);
                container.innerHTML = '';
            }
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // ── SAVE TO CART ─────────────────────────────────────
        function saveToCart() {
            const id    = document.getElementById('modalItemId').value;
            const name  = document.getElementById('modalItemName').innerText;
            const price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice);
            const qty   = parseInt(document.getElementById('modalQty').value);
            const type  = document.querySelector('input[name="orderType"]:checked').value;
            const notes = document.getElementById('modalNotes').value.trim();

            const variants = {};
            document.querySelectorAll('.variant-group').forEach(group => {
                const vName = group.dataset.variantName;
                const vVal  = group.querySelector('.variant-select')?.value || '';
                if (vName && vVal) variants[vName] = vVal;
            });

            // Format catatan agar varian rapi (Dine In • Pedas: Sedang • Catatan: bungkus plastik)
            const variantSummary = Object.entries(variants).map(([k, v]) => `${k}: ${v}`).join(' • ');
            const finalNotesArr = [type];
            if(variantSummary) finalNotesArr.push(variantSummary);
            if(notes) finalNotesArr.push(`Catatan: ${notes}`);
            
            const fullNotes = finalNotesArr.join(' • ');

            cart.unshift({
                menu_id: id,
                name: name,
                price: price,
                qty: qty,
                subtotal: price * qty,
                notes: fullNotes,
                variants: variants
            });

            closeAddModal();
            updateCartUI();
        }

        // ── Cart UI ──────────────────────────────────────────
        function updateCartUI() {
            let total = 0; let totalQty = 0;
            cart.forEach(item => { total += item.subtotal; totalQty += item.qty; });

            const floatingCart = document.getElementById('floating-cart');
            if (cart.length > 0) {
                floatingCart.classList.remove('hidden');
                document.getElementById('cart-qty').innerText = totalQty;
                document.getElementById('cart-total').innerText = formatRupiah(total);
            } else {
                floatingCart.classList.add('hidden');
            }

            document.getElementById('checkout-total').innerText = formatRupiah(total);
            document.getElementById('cart_data_input').value = JSON.stringify(cart);

            const container = document.getElementById('cart-container');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = `<div class="text-center text-gray-300 font-bold italic py-6">Belum ada pesanan</div>`;
                return;
            }

            cart.forEach((item, i) => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border dark:border-gray-700">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 pr-2">
                                <h4 class="font-bold text-sm dark:text-white">${escapeHtml(item.name)}</h4>
                                <p class="text-orange-500 font-semibold text-[10px] mt-0.5 leading-tight">${escapeHtml(item.notes)}</p>
                            </div>
                            <button type="button" onclick="removeItem(${i})" class="text-xs font-bold text-red-500 hover:text-red-700 ml-2">Hapus</button>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-lg text-xs font-black dark:text-white">x${item.qty}</span>
                            <span class="font-black text-sm dark:text-white">${formatRupiah(item.subtotal)}</span>
                        </div>
                    </div>
                `);
            });
        }

        function openCartModal() {
            updateCartUI();
            document.getElementById('cartModal').classList.replace('hidden', 'flex');
        }

        function removeItem(i) {
            cart.splice(i, 1);
            updateCartUI();
            if (cart.length === 0) closeModal('cartModal');
        }

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Pilih menu dulu sebelum kirim pesanan!');
                return;
            }
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        });
    </script>
</body>
</html>