<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        body { transition: background-color 0.3s, color 0.3s; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-100 relative">

    @if(session('error'))
        <div id="alert-error" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-3 rounded-2xl font-bold shadow-2xl animate-bounce">
            {{ session('error') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-error').remove(), 3000);</script>
    @endif
    @if(session('success'))
        <div id="alert-success" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-black text-white px-6 py-3 rounded-2xl font-bold shadow-2xl border-l-8 border-orange-500">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-success').remove(), 3000);</script>
    @endif

    <form action="{{ route('kasir.store') }}" method="POST" id="orderForm" class="flex h-screen overflow-hidden">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">

        {{-- ===== LEFT PANEL: MENU ===== --}}
        <div class="w-3/5 p-6 overflow-y-auto flex flex-col relative z-0 border-r dark:border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-orange-600 tracking-tight uppercase">Pilih Menu</h2>
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu..."
                           class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl focus:border-orange-500 outline-none font-semibold shadow-sm">
                </div>
            </div>

            <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md transition">Menu</button>
                <button type="button" onclick="filterMenu('Ter-favorit')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Ter-favorit</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Makanan Berat</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Minuman</button>
            </div>

            <div class="grid grid-cols-2 gap-6 pb-20" id="menuGrid">
                @foreach($menus as $menu)
                <div id="menu-item-{{ $menu->id }}"
                     onclick="openAddModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }}, '{{ $menu->category_name }}')"
                     class="menu-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 overflow-hidden transition hover:shadow-xl hover:border-orange-400 flex flex-col h-full cursor-pointer group"
                     data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    <div class="h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-sm italic font-medium uppercase text-center p-2">Foto {{ $menu->name }}</div>
                    <div class="p-5 flex flex-col flex-1 relative bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                        <h3 class="font-bold text-xl leading-tight mb-2 text-gray-800 dark:text-gray-100">{{ $menu->name }}</h3>
                        <p class="text-orange-500 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <div class="absolute bottom-4 right-4 bg-orange-100 dark:bg-orange-900/30 p-3 rounded-full text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" onclick="toggleDarkMode()" class="fixed bottom-6 left-6 p-4 bg-white dark:bg-gray-800 rounded-full shadow-2xl border dark:border-gray-700 z-50 hover:scale-110 transition active:scale-95">
                <svg id="sun-icon" class="w-6 h-6 text-orange-500 hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                <svg id="moon-icon" class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
            </button>
        </div>

        {{-- ===== RIGHT PANEL ===== --}}
        <div class="w-2/5 bg-white dark:bg-gray-800 p-6 shadow-2xl flex flex-col border-l dark:border-gray-700">

            {{-- Table Selector --}}
            <div class="mb-4 border-b-2 border-gray-100 dark:border-gray-700 pb-4 text-center">
                <input type="hidden" name="table_id" id="selected_table_id">
                <button type="button" onclick="openTableModal()"
                        class="w-full bg-white dark:bg-gray-800 text-black dark:text-white border-2 border-gray-100 dark:border-gray-700 p-4 rounded-2xl font-bold text-xl hover:border-orange-500 transition flex justify-center items-center shadow-sm relative group">
                    <span id="table_label" class="uppercase">Nomor Meja</span>
                    <svg class="w-6 h-6 text-orange-500 absolute right-4 group-hover:translate-y-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                </button>
            </div>

            {{-- Mode Tabs (hidden until table selected) --}}
            <div id="panel-tabs" class="hidden gap-2 mb-4">
                <button type="button" onclick="switchPanel('cart')" id="tab-cart"
                    class="flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-orange-500 bg-orange-500 text-white transition">
                    Pesanan Baru
                </button>
                <button type="button" onclick="switchPanel('order')" id="tab-order"
                    class="flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition">
                    Cek Meja
                </button>
            </div>

            {{-- CART PANEL --}}
            <div id="panel-cart" class="flex flex-col flex-1 overflow-hidden">
                <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-100 uppercase tracking-tight">Detail Pesanan</h2>
                <div id="cart-container" class="flex-1 overflow-y-auto pr-2 space-y-3">
                    <div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold">
                        <p>BELUM ADA MENU DIPILIH</p>
                    </div>
                </div>
                <div class="border-t-2 border-gray-100 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500 text-lg uppercase font-bold">Total</span>
                        <span id="total-price" class="text-orange-600 text-3xl font-bold">Rp 0</span>
                    </div>
                    <button type="button" onclick="validateAndSubmit()"
                        class="w-full bg-orange-500 text-white py-5 rounded-2xl font-bold text-2xl hover:bg-black dark:hover:bg-orange-600 shadow-xl transition transform active:scale-95 uppercase tracking-wider">
                        Kirim Pesanan
                    </button>
                </div>
            </div>

            {{-- ORDER VIEW PANEL --}}
            <div id="panel-order" class="flex-col flex-1 overflow-hidden hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 uppercase tracking-tight">Pesanan Aktif</h2>
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full uppercase">Pending</span>
                </div>
                <div id="order-container" class="flex-1 overflow-y-auto pr-2 space-y-3">
                    <div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold text-center">
                        <p>TIDAK ADA PESANAN<br>AKTIF DI MEJA INI</p>
                    </div>
                </div>
                <div class="border-t-2 border-gray-100 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-lg uppercase font-bold">Total</span>
                        <span id="order-total-price" class="text-orange-600 text-3xl font-bold">Rp 0</span>
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- ===== TABLE MODAL ===== --}}
    <div id="tableModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-[480px] rounded-3xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-center mb-8 uppercase dark:text-white">Denah Meja</h2>
            <div class="grid grid-cols-4 gap-4 mb-6">
                @for ($i = 1; $i <= 12; $i++)
                    <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}"
                            class="meja-option aspect-square flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-orange-500 transition-all active:scale-90 shadow-sm font-bold group">
                        <span class="text-[10px] text-gray-300 group-hover:text-orange-400 uppercase">MEJA</span>
                        <span class="text-2xl text-black dark:text-white group-hover:text-orange-600">{{ $i }}</span>
                    </button>
                @endfor
            </div>

            {{-- Takeaway Button --}}
            <button type="button" onclick="selectTakeaway()"
                    id="btn-takeaway"
                    class="w-full mb-4 flex items-center justify-center gap-3 border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 text-gray-700 dark:text-gray-300 hover:text-orange-600 py-4 rounded-2xl font-bold text-lg transition active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/></svg>
                TAKEAWAY
            </button>

            <button type="button" onclick="closeTableModal()" class="w-full bg-black dark:bg-orange-600 text-white py-3 rounded-xl font-bold uppercase transition hover:bg-orange-600">Tutup</button>
        </div>
    </div>

    {{-- ===== ITEM MODAL ===== --}}
    <div id="itemModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-[420px] rounded-[2.5rem] shadow-2xl p-8" id="modalContent">
            <div class="flex justify-between items-start mb-6 border-b dark:border-gray-700 pb-4">
                <div>
                    <h2 id="modalItemName" class="text-2xl font-bold text-gray-800 dark:text-white uppercase">Nama Item</h2>
                    <p id="modalItemPrice" class="text-orange-500 font-bold text-xl">Rp 0</p>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>
                </button>
            </div>

            <input type="hidden" id="modalItemId">
            <input type="hidden" id="modalEditIndex" value="-1">

            <div class="space-y-6">
                <div id="chickenPartContainer" class="hidden">
                    <label class="block font-bold text-gray-600 dark:text-gray-400 text-sm mb-2 uppercase italic">Bagian Ayam:</label>
                    <select id="chickenPart" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl focus:border-orange-500 font-bold bg-gray-50 dark:bg-gray-700 dark:text-white outline-none">
                        <option value="Bebas">Bebas</option><option value="Dada">Dada</option><option value="Paha">Paha</option><option value="Sayap">Sayap</option>
                    </select>
                </div>

                <div id="spicyLevelContainer" class="hidden">
                    <label class="block font-bold text-gray-600 dark:text-gray-400 text-sm mb-2 uppercase italic">Pedas:</label>
                    <select id="spicyLevel" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl focus:border-orange-500 font-bold bg-gray-50 dark:bg-gray-700 dark:text-white outline-none">
                        <option value="Tidak Pedas">Tidak Pedas</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Pedas">Pedas</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-600 dark:text-gray-400 text-sm mb-2 uppercase italic">Jumlah:</label>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-700 p-2 rounded-2xl w-fit border dark:border-gray-600">
                        <button type="button" onclick="changeQty(-1)" class="w-10 h-10 bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-sm font-black text-xl hover:bg-orange-500 hover:text-white transition">-</button>
                        <input type="number" id="modalQty" value="1" min="1" class="w-12 text-center font-bold text-xl bg-transparent outline-none dark:text-white" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-10 h-10 bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-sm font-black text-xl hover:bg-orange-500 hover:text-white transition">+</button>
                    </div>
                </div>

                <div class="pt-4 border-t dark:border-gray-700">
                    <label class="block font-bold text-gray-600 dark:text-gray-400 text-sm mb-3 uppercase italic">Tipe Pesanan:</label>
                    <div class="flex gap-4">
                        <label id="label-dinein" class="flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold transition-all">
                            <input type="radio" name="orderType" value="Dine In" class="hidden" checked onchange="toggleServiceUI()"> Dine In
                        </label>
                        <label id="label-takeaway" class="flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold transition-all">
                            <input type="radio" name="orderType" value="Takeaway" class="hidden" onchange="toggleServiceUI()"> Takeaway
                        </label>
                    </div>
                </div>

                <button type="button" onclick="saveToCart()" id="btn-submit-modal" class="w-full bg-black dark:bg-orange-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-orange-600 transition uppercase tracking-widest">Tambahkan</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // Pending orders dari Laravel (keyed by table_id)
        const pendingOrders = @json($pendingOrders);

        // =====================
        // DARK MODE
        // =====================
        function toggleDarkMode() {
            const html = document.documentElement;
            const sun = document.getElementById('sun-icon');
            const moon = document.getElementById('moon-icon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
                localStorage.theme = 'dark';
            }
        }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.getElementById('sun-icon').classList.remove('hidden');
            document.getElementById('moon-icon').classList.add('hidden');
        }

        // =====================
        // SUBMIT
        // =====================
        function validateAndSubmit() {
            const tableId = document.getElementById('selected_table_id').value;
            if (!tableId) {
                alert("Waduh, Nomor Meja belum dipilih rek!");
                openTableModal();
                return;
            }
            if (cart.length === 0) {
                alert("Keranjang masih kosong!");
                return;
            }
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            document.getElementById('orderForm').submit();
        }

        // =====================
        // TABLE MODAL
        // =====================
        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }

        function selectTable(num) {
            document.getElementById('table_label').innerText = "MEJA " + num;
            document.getElementById('selected_table_id').value = num;

            // Reset all table buttons
            document.querySelectorAll('.meja-option').forEach(b => {
                b.classList.remove('border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
                b.classList.add('border-gray-100', 'bg-white', 'dark:bg-gray-800');
            });
            // Reset takeaway button
            document.getElementById('btn-takeaway').classList.remove('border-orange-500', 'bg-orange-50', 'text-orange-600');
            document.getElementById('btn-takeaway').classList.add('border-gray-100', 'bg-white', 'dark:bg-gray-800');

            // Highlight selected table
            const active = document.getElementById('btn-meja-' + num);
            active.classList.replace('border-gray-100', 'border-orange-500');
            active.classList.replace('bg-white', 'bg-orange-50');

            // Show tabs
            const tabs = document.getElementById('panel-tabs');
            tabs.classList.remove('hidden');
            tabs.classList.add('flex');

            // Auto-switch panel
            if (pendingOrders[num]) {
                switchPanel('order');
            } else {
                switchPanel('cart');
            }

            setTimeout(closeTableModal, 200);
        }

        function selectTakeaway() {
            document.getElementById('table_label').innerText = "TAKEAWAY";
            document.getElementById('selected_table_id').value = "takeaway";

            // Reset all table buttons
            document.querySelectorAll('.meja-option').forEach(b => {
                b.classList.remove('border-orange-500', 'bg-orange-50');
                b.classList.add('border-gray-100', 'bg-white', 'dark:bg-gray-800');
            });

            // Highlight takeaway button
            const btn = document.getElementById('btn-takeaway');
            btn.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
            btn.classList.remove('border-gray-100');

            // Show tabs, switch to cart
            const tabs = document.getElementById('panel-tabs');
            tabs.classList.remove('hidden');
            tabs.classList.add('flex');
            switchPanel('cart');

            // Pre-select Takeaway in item modal
            document.querySelector('input[name="orderType"][value="Takeaway"]').checked = true;
            toggleServiceUI();

            setTimeout(closeTableModal, 200);
        }

        // =====================
        // PANEL SWITCH (Cart / Cek Meja)
        // =====================
        function switchPanel(panel) {
            const cartPanel = document.getElementById('panel-cart');
            const orderPanel = document.getElementById('panel-order');
            const tabCart = document.getElementById('tab-cart');
            const tabOrder = document.getElementById('tab-order');

            if (panel === 'cart') {
                cartPanel.classList.remove('hidden'); cartPanel.classList.add('flex');
                orderPanel.classList.add('hidden'); orderPanel.classList.remove('flex');
                tabCart.className = 'flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-orange-500 bg-orange-500 text-white transition';
                tabOrder.className = 'flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition';
            } else {
                orderPanel.classList.remove('hidden'); orderPanel.classList.add('flex');
                cartPanel.classList.add('hidden'); cartPanel.classList.remove('flex');
                tabOrder.className = 'flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-orange-500 bg-orange-500 text-white transition';
                tabCart.className = 'flex-1 py-2 rounded-xl font-bold text-sm uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition';
                loadOrderPanel();
            }
        }

        function loadOrderPanel() {
            const tableId = document.getElementById('selected_table_id').value;
            const container = document.getElementById('order-container');
            const totalEl = document.getElementById('order-total-price');

            if (!tableId) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold text-center"><p>PILIH MEJA DULU</p></div>`;
                totalEl.innerText = 'Rp 0';
                return;
            }

            const order = pendingOrders[tableId];

            if (!order) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold text-center"><p>TIDAK ADA PESANAN<br>AKTIF DI MEJA INI</p></div>`;
                totalEl.innerText = 'Rp 0';
                return;
            }

            container.innerHTML = '';

            // Order header
            container.insertAdjacentHTML('beforeend', `
                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-2xl p-3 mb-1">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-widest">Meja ${tableId} • ${order.order_number}</p>
                </div>
            `);

            order.order_items.forEach(item => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 shadow-sm">
                        <h4 class="font-bold text-black dark:text-white text-lg uppercase">${item.menu ? item.menu.name : 'Menu'}</h4>
                        <p class="text-[10px] font-bold text-orange-500 tracking-widest mt-1 uppercase italic">${item.notes ?? '-'}</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="bg-black dark:bg-orange-600 text-white px-2 py-0.5 rounded-lg text-xs font-bold uppercase">x${item.quantity}</span>
                            <span class="text-black dark:text-white font-bold text-lg">${formatRupiah(item.subtotal)}</span>
                        </div>
                    </div>
                `);
            });

            totalEl.innerText = formatRupiah(order.total_price);
        }

        // =====================
        // ITEM MODAL
        // =====================
        function openAddModal(id, name, price, cat) {
            resetModalFields();
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalEditIndex').value = -1;
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;
            checkVisibility(name, cat);
            document.getElementById('btn-submit-modal').innerText = "Tambahkan";
            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function openEditModal(index) {
            const item = cart[index];
            resetModalFields();
            document.getElementById('modalItemId').value = item.menu_id;
            document.getElementById('modalEditIndex').value = index;
            document.getElementById('modalItemName').innerText = item.name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(item.price);
            document.getElementById('modalItemPrice').dataset.rawPrice = item.price;
            document.getElementById('modalQty').value = item.qty;
            document.getElementById('btn-submit-modal').innerText = "Update Item";
            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function closeModal() { document.getElementById('itemModal').classList.replace('flex', 'hidden'); }
        function changeQty(v) { let q = document.getElementById('modalQty'); if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v; }

        function resetModalFields() {
            document.getElementById('modalQty').value = 1;
            document.getElementById('spicyLevel').value = "Tidak Pedas";
            document.getElementById('chickenPart').value = "Bebas";
            document.querySelector('input[name="orderType"][value="Dine In"]').checked = true;
            toggleServiceUI();
        }

        function checkVisibility(name, cat) {
            const n = name.toLowerCase();
            document.getElementById('chickenPartContainer').classList.toggle('hidden', !n.includes('ayam'));
            document.getElementById('spicyLevelContainer').classList.toggle('hidden', cat.toLowerCase() === 'minuman');
        }

        // =====================
        // CART
        // =====================
        function saveToCart() {
            let editIndex = parseInt(document.getElementById('modalEditIndex').value);
            let id = document.getElementById('modalItemId').value;
            let name = document.getElementById('modalItemName').innerText;
            let price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice);
            let qty = parseInt(document.getElementById('modalQty').value);
            let type = document.querySelector('input[name="orderType"]:checked').value;
            let spicy = !document.getElementById('spicyLevelContainer').classList.contains('hidden') ? document.getElementById('spicyLevel').value : null;
            let part = !document.getElementById('chickenPartContainer').classList.contains('hidden') ? document.getElementById('chickenPart').value : null;

            let noteParts = [type];
            if (part && part !== 'Bebas') noteParts.push(part);
            if (spicy) noteParts.push(spicy);
            let notes = noteParts.join(' • ');

            const itemData = { menu_id: id, name, price, qty, subtotal: price * qty, notes };

            if (editIndex > -1) {
                cart[editIndex] = itemData;
            } else {
                let dup = cart.findIndex(i => i.menu_id === id && i.notes === notes);
                if (dup > -1) {
                    cart[dup].qty += qty;
                    cart[dup].subtotal = cart[dup].qty * price;
                } else {
                    cart.push(itemData);
                }
            }
            closeModal();
            updateCartUI();
        }

        function updateCartUI() {
            let container = document.getElementById('cart-container');
            let totalEl = document.getElementById('total-price');
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            container.innerHTML = '';
            let total = 0;

            if (cart.length === 0) {
                container.innerHTML = `<div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold"><p>BELUM ADA MENU DIPILIH</p></div>`;
                totalEl.innerText = 'Rp 0';
                return;
            }

            cart.forEach((item, i) => {
                total += item.subtotal;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 flex justify-between items-center shadow-sm hover:border-orange-500 transition">
                        <div class="flex-1 cursor-pointer" onclick="openEditModal(${i})">
                            <h4 class="font-bold text-black dark:text-white text-lg uppercase">${item.name}</h4>
                            <p class="text-[10px] font-bold text-orange-500 tracking-widest mt-1 mb-2 uppercase italic">${item.notes}</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-black dark:bg-orange-600 text-white px-2 py-0.5 rounded-lg text-xs font-bold uppercase">x${item.qty}</span>
                                <span class="text-black dark:text-white font-bold text-lg">${formatRupiah(item.subtotal)}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 ml-4">
                            <button type="button" onclick="openEditModal(${i})" class="text-gray-300 hover:text-orange-500 transition p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                            </button>
                            <button type="button" onclick="removeItem(${i})" class="text-gray-300 hover:text-red-500 transition p-1">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                            </button>
                        </div>
                    </div>
                `);
            });
            totalEl.innerText = formatRupiah(total);
        }

        function removeItem(i) {
            if (cart[i].qty > 1) {
                cart[i].qty -= 1;
                cart[i].subtotal = cart[i].qty * cart[i].price;
            } else {
                cart.splice(i, 1);
            }
            updateCartUI();
        }

        // =====================
        // FILTER & SEARCH
        // =====================
        function searchMenu() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => card.style.display = card.getAttribute('data-name').includes(input) ? 'flex' : 'none');
        }

        function filterMenu(kat) {
            document.querySelectorAll('.menu-card').forEach(card => card.style.display = (kat === 'semua' || card.getAttribute('data-category') === kat) ? 'flex' : 'none');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                let active = btn.innerText === (kat === 'semua' ? 'Menu' : kat);
                btn.className = active ? 'filter-btn bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md' : 'filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 transition';
            });
        }

        // =====================
        // ORDER TYPE UI
        // =====================
        function toggleServiceUI() {
            const isDineIn = document.querySelector('input[name="orderType"]:checked').value === 'Dine In';
            const dBtn = document.getElementById('label-dinein');
            const tBtn = document.getElementById('label-takeaway');
            if (isDineIn) {
                dBtn.className = "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-600";
                tBtn.className = "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-gray-100 dark:border-gray-700 text-gray-500 dark:bg-gray-800";
            } else {
                tBtn.className = "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-600";
                dBtn.className = "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-gray-100 dark:border-gray-700 text-gray-500 dark:bg-gray-800";
            }
        }
    </script>
</body>
</html>