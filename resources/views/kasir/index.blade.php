<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    {{-- ★ MIDTRANS SNAP JS — ganti SB-Mid-client-xxx dengan Client Key kamu --}}
    {{-- Kalau belum pakai Midtrans, baris ini bisa dihapus dulu --}}
    {{-- <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script> --}}

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        body { transition: background-color 0.3s, color 0.3s; }

        /* ★ PRINT STYLES — hanya aktif saat window.print() */
        @media print {
            body * { visibility: hidden !important; }
            #nota-printable, #nota-printable * { visibility: visible !important; }
            #nota-printable {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 80mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }

        /* ★ NOTA THERMAL STYLES */
        #nota-printable {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            width: 270px;
            padding: 6px;
        }
        .nota-center { text-align: center; }
        .nota-bold { font-weight: bold; }
        .nota-row { display: flex; justify-content: space-between; align-items: flex-start; }
        .nota-item-name { flex: 1; padding-right: 6px; }
        .nota-item-price { white-space: nowrap; font-weight: bold; }
        .nota-divider-solid { border: none; border-top: 1px solid #000; margin: 5px 0; }
        .nota-divider-dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }
        .nota-harga-satuan { padding-left: 14px; color: #444; font-size: 10px; }
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
        <div id="alert-success" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-black text-white px-6 py-3 rounded-2xl font-bold shadow-2xl border-l-8 border-green-500">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-success').remove(), 3000);</script>
    @endif

    {{-- FORM UTAMA KASIR --}}
    <form action="{{ route('kasir.store') }}" method="POST" id="orderForm" class="flex h-screen overflow-hidden">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">
        <input type="hidden" name="customer_name" id="customer_name_input">
        <input type="hidden" name="payment_type" id="payment_type">
        <input type="hidden" name="payment_method" id="payment_method" value="Belum Bayar">

        {{-- ===== LEFT PANEL: MENU ===== --}}
        <div class="w-3/5 p-6 overflow-y-auto flex flex-col relative border-r dark:border-gray-700">
            <div class="flex justify-between items-center mb-6 flex-shrink-0">
                <h2 class="text-3xl font-bold text-orange-600 tracking-tight uppercase">KASIR - ULAM SARI</h2>
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu..."
                        class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl focus:border-orange-500 outline-none font-semibold shadow-sm">
                </div>
            </div>

            <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide flex-shrink-0">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md transition">Menu</button>
                <button type="button" onclick="filterMenu('Ter-favorit')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Ter-favorit</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Makanan Berat</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2 rounded-full font-semibold border dark:border-gray-700 hover:bg-orange-50 hover:text-orange-500 transition">Minuman</button>
            </div>

            <div class="grid grid-cols-2 gap-6 pb-20" id="menuGrid">
                @foreach($menus as $menu)
                <div onclick="openAddModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->category_name) }}')"
                    class="menu-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 overflow-hidden transition hover:shadow-xl hover:border-orange-400 flex flex-col h-full cursor-pointer group"
                    data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="h-64 w-full object-cover object-center border-b dark:border-gray-700">
                    @else
                        <div class="h-64 bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-sm italic font-medium uppercase text-center p-2">FOTO<br>{{ $menu->name }}</div>
                    @endif
                    <div class="p-5 flex flex-col flex-1 relative bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                        <h3 class="font-bold text-xl leading-tight mb-2 text-gray-800 dark:text-gray-100">{{ $menu->name }}</h3>
                        <p class="text-orange-500 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== RIGHT PANEL ===== --}}
        <div class="w-2/5 bg-white dark:bg-gray-800 p-6 shadow-2xl flex flex-col border-l dark:border-gray-700">
            <div class="mb-4 border-b-2 border-gray-100 dark:border-gray-700 pb-4">
                <input type="hidden" name="table_id" id="selected_table_id">
                <button type="button" onclick="openTableModal()"
                        class="w-full bg-white dark:bg-gray-800 text-black dark:text-white border-2 border-gray-100 dark:border-gray-700 p-4 rounded-2xl font-bold text-xl hover:border-orange-500 transition flex justify-center items-center shadow-sm relative group">
                    <span id="table_label" class="uppercase">Nomor Meja</span>
                </button>
            </div>

            <div id="panel-tabs" class="flex gap-2 mb-4">
                <button type="button" onclick="switchPanel('cart')" id="tab-cart"
                    class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white transition">Pesanan Baru</button>
                <button type="button" onclick="switchPanel('order')" id="tab-order"
                    class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition">Cek Meja</button>
                <button type="button" onclick="switchPanel('history')" id="tab-history"
                    class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition">Riwayat</button>
            </div>

            {{-- CART PANEL --}}
            <div id="panel-cart" class="flex flex-col flex-1 overflow-hidden">
                <div id="cart-container" class="flex-1 overflow-y-auto pr-2 space-y-3">
                    <div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold"><p>BELUM ADA MENU DIPILIH</p></div>
                </div>
                <div class="border-t-2 border-gray-100 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500 text-lg uppercase font-bold tracking-wider">Total</span>
                        <span id="total-price" class="text-orange-600 text-3xl font-black">Rp 0</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="validateAndSubmit('later')" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-4 rounded-2xl font-bold text-sm transition uppercase">Bayar Nanti</button>
                        <button type="button" onclick="validateAndSubmit('now')" class="bg-orange-500 text-white py-4 rounded-2xl font-bold text-sm shadow-lg transition uppercase">Bayar Sekarang</button>
                    </div>
                </div>
            </div>

            {{-- ORDER VIEW PANEL --}}
            <div id="panel-order" class="flex-col flex-1 overflow-hidden hidden">
                <div id="order-container" class="flex-1 overflow-y-auto pr-2 space-y-3"></div>
                <div class="border-t-2 border-gray-100 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-lg uppercase font-bold">Total Meja</span>
                        <span id="order-total-price" class="text-orange-600 text-3xl font-bold">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- HISTORY VIEW PANEL --}}
            <div id="panel-history" class="flex-col flex-1 overflow-hidden hidden">
                <div class="flex gap-2 items-center mb-4">
                    <div class="flex items-center gap-1 bg-white dark:bg-gray-700 p-1 px-2 rounded-xl border dark:border-gray-600 shadow-sm">
                        <div class="flex flex-col">
                            <span class="text-[7px] font-bold text-gray-400 uppercase">Mulai</span>
                            <input type="date" id="start_date" class="bg-transparent text-[10px] font-bold text-orange-600 outline-none">
                        </div>
                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-600 mx-1"></div>
                        <div class="flex flex-col">
                            <span class="text-[7px] font-bold text-gray-400 uppercase">Selesai</span>
                            <input type="date" id="end_date" class="bg-transparent text-[10px] font-bold text-orange-600 outline-none">
                        </div>
                    </div>
                    <button type="button" onclick="exportExcel()" class="bg-green-600 text-white text-[10px] font-bold px-3 py-3 rounded-xl uppercase transition shadow-md">Excel</button>
                </div>
                
                <div class="flex-1 overflow-y-auto pr-2 space-y-3">
                    @forelse($historyOrders ?? [] as $history)
                        <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 shadow-sm hover:border-green-400 transition">
                            <div class="flex justify-between items-start mb-2 border-b dark:border-gray-700 pb-2">
                                <div>
                                    <h4 class="font-bold text-black dark:text-white uppercase">{{ $history->order_number }}</h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">
                                        {{ $history->created_at->format('d/m/Y H:i') }} • 
                                        {{ $history->table_id == '0' ? 'TAKEAWAY' : 'MEJA ' . $history->table_id }}
                                    </p>
                                </div>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $history->payment_method }}</span>
                            </div>

                            {{-- ★ TAMBAHAN 1: INFO PELANGGAN DI PANEL RIWAYAT ★ --}}
                            <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 text-xs">
                                <p><span class="font-bold text-gray-500">Pemesan:</span> <span class="font-black uppercase">{{ $history->customer_name ?? 'Tanpa Nama' }}</span></p>
                                <p><span class="font-bold text-gray-500">No. HP:</span> <span class="font-bold text-blue-600">{{ $history->phone_number ?? '-' }}</span></p>
                            </div>

                            <div class="flex justify-between items-center mt-3">
                                <span class="text-xs font-bold text-gray-500 uppercase">{{ $history->orderItems->sum('quantity') }} Item</span>
                                <span class="font-black text-green-600">Rp {{ number_format($history->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold text-center"><p>BELUM ADA TRANSAKSI</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </form>

    {{-- PAYMENT MODAL --}}
    <div class="space-y-3 mb-4">
    <div>
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
            Nama Pelanggan <span class="text-red-500">*</span>
        </label>
        <input type="text" name="customer_name" required placeholder="Masukkan nama pelanggan" 
               class="w-full p-2.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
            No. WhatsApp (Opsional)
        </label>
        <input type="text" name="phone_number" placeholder="Contoh: 0812345678" 
               class="w-full p-2.5 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
    </div>
</div>

    {{-- TABLE MODAL --}}
    <div id="tableModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl p-8">
            <h2 class="text-2xl font-bold text-center mb-6 uppercase dark:text-white">Denah Meja</h2>
            <div class="grid grid-cols-4 gap-3 mb-6">
                @for ($i = 1; $i <= 12; $i++)
                @php $hasOrder = isset($pendingOrders[$i]) && count($pendingOrders[$i]) > 0; @endphp
                <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}"
                    class="meja-option aspect-square flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 font-bold relative hover:border-orange-500 transition">
                    <span class="text-[10px] text-gray-400">MEJA</span>
                    <span class="text-xl dark:text-white">{{ $i }}</span>
                    @if($hasOrder)
                        <span class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full animate-pulse indicator-dot"></span>
                    @endif
                </button>
                @endfor
            </div>
            <button type="button" onclick="selectTakeaway()" id="btn-takeaway-ui" class="w-full mb-4 py-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white hover:border-orange-500 transition">Take Away (Bungkus)</button>
            <button type="button" onclick="closeTableModal()" class="w-full text-gray-400 font-bold uppercase text-xs transition">Batal</button>
        </div>
    </div>

    {{-- MODAL TAMBAH ITEM (QTY & CATATAN) --}}
    <div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-sm rounded-3xl p-6">
            <h3 id="modalName" class="text-xl font-bold mb-1 dark:text-white">Nama Menu</h3>
            <p id="modalPrice" class="text-orange-500 font-bold mb-4">Rp 0</p>
            <input type="hidden" id="modalItemId">

            <div class="flex items-center justify-between mb-4 border-t-2 border-b-2 border-gray-100 dark:border-gray-700 py-4">
                <span class="font-bold text-gray-500">Jumlah</span>
                <div class="flex items-center gap-4">
                    <button type="button" onclick="changeQty(-1)" class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-xl font-bold dark:text-white">-</button>
                    <input type="number" id="modalQty" value="1" min="1" class="w-12 text-center font-bold text-xl bg-transparent outline-none dark:text-white" readonly>
                    <button type="button" onclick="changeQty(1)" class="w-10 h-10 bg-orange-500 text-white rounded-xl font-bold">+</button>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Catatan Tambahan</label>
                <input type="text" id="modalNotes" placeholder="Cth: Pedas, Tanpa Daun Bawang..." class="w-full border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-900 rounded-xl p-3 text-sm font-semibold outline-none focus:border-orange-500">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeAddModal()" class="flex-1 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-gray-500 hover:border-gray-400 transition">Batal</button>
                <button type="button" onclick="saveToCart()" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold hover:bg-gray-800 transition shadow-lg">Tambah</button>
            </div>
        </div>
    </div>

    {{-- ★ MODAL PRINT NOTA (THERMAL) --}}
    <div id="printModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 w-full max-w-sm">
            <div class="w-full flex justify-between items-center mb-4">
                <h3 class="text-lg font-black uppercase dark:text-white">Preview Nota</h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-red-500 font-bold text-xl">✕</button>
            </div>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 mb-4 w-full flex justify-center bg-gray-50 overflow-auto">
                <div id="nota-printable-container">
                    {{-- Ini div bayangan buat nampilin preview aja --}}
                    <div id="nota-printable">
                        <p style="text-align:center;padding:20px;color:#999;font-family:sans-serif;">Memuat nota...</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 w-full">
                <button onclick="closePrintModal()" class="flex-1 py-3 border-2 border-gray-200 rounded-2xl font-bold text-gray-500 hover:border-gray-400 transition text-sm">
                    Tutup
                </button>
                <button onclick="window.print()" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold hover:bg-gray-800 transition text-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak Nota
                </button>
            </div>
        </div>
    </div>


    <script>
        let cart = [];
        let pendingOrders = @json($pendingOrders ?? []);
        let activePanel = 'cart';

        const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

        function switchPanel(panel) {
            activePanel = panel;
            document.getElementById('panel-cart').classList.toggle('hidden', panel !== 'cart');
            document.getElementById('panel-order').classList.toggle('hidden', panel !== 'order');
            document.getElementById('panel-history').classList.toggle('hidden', panel !== 'history');

            document.getElementById('tab-cart').className = panel === 'cart' ? 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white transition' : 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition';
            document.getElementById('tab-order').className = panel === 'order' ? 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white transition' : 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition';
            document.getElementById('tab-history').className = panel === 'history' ? 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white transition' : 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition';

            if (panel === 'order') loadOrderPanel();
        }

        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }

        function selectTable(id) {
            document.getElementById('selected_table_id').value = id;
            document.getElementById('table_label').innerText = 'MEJA ' + id;
            
            document.querySelectorAll('.meja-option').forEach(el => {
                el.classList.remove('border-orange-500');
                el.classList.add('border-gray-100');
            });
            document.getElementById('btn-takeaway-ui').classList.remove('border-orange-500');
            document.getElementById('btn-meja-' + id).classList.remove('border-gray-100');
            document.getElementById('btn-meja-' + id).classList.add('border-orange-500');
            
            closeTableModal();
            loadOrderPanel();
        }

        function selectTakeaway() {
            document.getElementById('selected_table_id').value = '0';
            document.getElementById('table_label').innerText = 'TAKEAWAY';
            
            document.querySelectorAll('.meja-option').forEach(el => {
                el.classList.remove('border-orange-500');
                el.classList.add('border-gray-100');
            });
            document.getElementById('btn-takeaway-ui').classList.add('border-orange-500');
            
            closeTableModal();
            loadOrderPanel();
        }

        function accPesanan(id) {
            if(confirm("Apakah pelanggan sudah membayar? Pesanan akan di-ACC dan masuk ke layar Dapur.")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/kasir/konfirmasi/' + id;
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
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

            const rawOrder = pendingOrders[tableId];
            if (!rawOrder || Object.keys(rawOrder).length === 0) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-400 italic font-bold text-center"><p>TIDAK ADA PESANAN AKTIF</p></div>`;
                totalEl.innerText = 'Rp 0';
                return;
            }

            let list = Array.isArray(rawOrder) ? [...rawOrder] : Object.values(rawOrder);
            container.innerHTML = '';
            let gTotal = 0;
            list.reverse();

            list.forEach((ord, idx) => {
                gTotal += parseInt(ord.total_price);
                let statusBadge = '';
                let btnKonfirmasi = '';

                if (ord.order_status_id == 4) {
                    statusBadge = `<span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase animate-pulse">Belum Bayar</span>`;
                    btnKonfirmasi = `
                    <div class="mt-3 border-t border-orange-200 dark:border-orange-800 pt-3 space-y-2">
                        <button type="button" onclick="accPesanan(${ord.id})" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95">
                            Terima Uang & ACC ke Dapur
                        </button>
                        <button type="button" onclick="openPrintModal(${ord.id})" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak Nota
                        </button>
                    </div>`;
                } else {
                    statusBadge = `<span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded uppercase">Sedang Diproses</span>`;
                    btnKonfirmasi = `
                    <div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-3">
                        <button type="button" onclick="openPrintModal(${ord.id})" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Cetak Nota
                        </button>
                    </div>`;
                }

                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-2xl p-4 mt-2 mb-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs font-bold text-orange-600 uppercase">Pesanan #${list.length - idx}
                            <br><span class="text-gray-500 text-[10px]">${ord.order_number}</span></p>
                            ${statusBadge}
                        </div>

                        {{-- ★ TAMBAHAN 2: INFO PELANGGAN DI KARTU CEK MEJA ★ --}}
                        <div class="mt-2 mb-2 p-2 bg-white dark:bg-gray-800 rounded border border-orange-100 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300">
                            <p><span class="font-bold text-gray-500">Pemesan:</span> <strong class="uppercase">${ord.customer_name ? ord.customer_name : 'Tanpa Nama'}</strong></p>
                            <p><span class="font-bold text-gray-500">No. HP:</span> <strong class="text-blue-600">${ord.phone_number ? ord.phone_number : '-'}</strong></p>
                        </div>

                        <div id="order-items-${ord.id}" class="space-y-2"></div>
                        ${btnKonfirmasi}
                    </div>
                `);

                const itemContainer = document.getElementById(`order-items-${ord.id}`);
                ord.order_items.forEach(item => {
                    const itemName = item.menu ? item.menu.name : (item.name || 'Menu');
                    itemContainer.insertAdjacentHTML('beforeend', `
                        <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-xl p-3 shadow-sm flex justify-between items-center">
                            <h4 class="font-bold uppercase text-sm dark:text-white">${itemName}</h4>
                            <div class="flex gap-4"><span class="bg-black text-white px-2 rounded font-bold">x${item.quantity}</span><span class="font-bold dark:text-white">${formatRupiah(item.subtotal)}</span></div>
                        </div>
                    `);
                });
            });

            totalEl.innerText = formatRupiah(gTotal);
        }

        function openAddModal(id, name, price, cat) {
            document.getElementById('modalQty').value = 1;
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalPrice').innerText = formatRupiah(price);
            document.getElementById('modalPrice').dataset.rawPrice = price;
            document.getElementById('modalNotes').value = "";
            document.getElementById('addModal').classList.replace('hidden', 'flex');
        }

        function closeAddModal() { document.getElementById('addModal').classList.replace('flex', 'hidden'); }

        function changeQty(v) {
            const q = document.getElementById('modalQty');
            if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v;
        }

        function saveToCart() {
            const id = document.getElementById('modalItemId').value;
            const name = document.getElementById('modalName').innerText;
            const price = parseInt(document.getElementById('modalPrice').dataset.rawPrice);
            const qty = parseInt(document.getElementById('modalQty').value);
            const notes = document.getElementById('modalNotes').value || '-';

            cart.push({ menu_id: id, name, price, qty, subtotal: price * qty, notes });
            closeAddModal();
            updateCartUI();
        }

        function updateCartUI() {
            let total = 0;
            const container = document.getElementById('cart-container');
            
            if (cart.length === 0) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 italic font-bold"><p>BELUM ADA MENU DIPILIH</p></div>`;
                document.getElementById('total-price').innerText = 'Rp 0';
                document.getElementById('cart_data_input').value = "";
                return;
            }

            container.innerHTML = '';
            cart.forEach((item, i) => {
                total += item.subtotal;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 shadow-sm flex flex-col gap-2 relative group hover:border-orange-500 transition">
                        <div class="flex justify-between items-start pr-8">
                            <div>
                                <h4 class="font-bold text-sm leading-tight dark:text-white uppercase">${item.name}</h4>
                                <p class="text-orange-500 font-bold text-sm mt-1">${formatRupiah(item.price)}</p>
                            </div>
                            <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-xl text-sm font-black dark:text-white">x${item.qty}</span>
                        </div>
                        ${item.notes !== '-' ? `<div class="bg-orange-50 dark:bg-orange-900/20 text-orange-600 px-3 py-2 rounded-xl text-xs font-semibold">Catatan: ${item.notes}</div>` : ''}
                        <button type="button" onclick="removeItem(${i})" class="absolute top-4 right-4 text-gray-300 hover:text-red-500 transition font-bold text-xl">✕</button>
                    </div>
                `);
            });

            document.getElementById('total-price').innerText = formatRupiah(total);
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        }

        function removeItem(i) {
            cart.splice(i, 1);
            updateCartUI();
        }

        function validateAndSubmit(type) {
            if (!document.getElementById('selected_table_id').value) {
                alert("Pilih Meja dulu rek!");
                openTableModal();
                return;
            }
            if (cart.length === 0) {
                alert("Keranjang kosong!");
                return;
            }

            document.getElementById('payment_type').value = type;

            if (type === 'now') {
                document.getElementById('paymentModal').classList.replace('hidden', 'flex');
            } else {
                document.getElementById('payment_method').value = "Belum Bayar";
                document.getElementById('orderForm').submit();
            }
        }

        function closePaymentModal() { document.getElementById('paymentModal').classList.replace('flex', 'hidden'); }

        function submitFinal(method) {
            document.getElementById('payment_method').value = method;
            document.getElementById('orderForm').submit();
        }

        function searchMenu() {
            const val = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = c.dataset.name.includes(val) ? 'flex' : 'none');
        }

        function filterMenu(k) {
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = (k === 'semua' || c.dataset.category === k) ? 'flex' : 'none');
        }

        function exportExcel() {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            if (!start || !end) {
                alert('Pilih tanggal Mulai & Selesai dulu!');
                return;
            }
            window.location.href = "{{ route('kasir.export') }}?start_date=" + start + "&end_date=" + end;
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // ★ FUNGSI PRINT NOTA (BARU)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        function closePrintModal() {
            document.getElementById('printModal').classList.replace('flex', 'hidden');
        }

        // Buka modal dan fetch data nota dari server
        async function openPrintModal(orderId) {
            const modal = document.getElementById('printModal');
            const el = document.getElementById('nota-printable');
            
            modal.classList.replace('hidden', 'flex');
            el.innerHTML = '<p style="text-align:center;padding:20px;color:#999;font-family:sans-serif;">Memuat nota...</p>';

            try {
                const res = await fetch(`/kasir/nota/${orderId}`);
                if (!res.ok) throw new Error("Gagal mengambil nota");
                const order = await res.json();

                const dt = new Date(order.created_at).toLocaleString('id-ID');
                const mejaTxt = order.table_id == '0' ? 'TAKEAWAY' : 'MEJA ' + order.table_id;
                
                const rp = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');

                let itemsHTML = '';
                for (let item of order.order_items) {
                    let qty = parseInt(item.quantity);
                    let hargaSatuan = Math.round(item.subtotal / qty);
                    itemsHTML += `
                        <div class="nota-row">
                            <span class="nota-item-name">${qty}x ${item.name}</span>
                            <span class="nota-item-price">${rp(item.subtotal)}</span>
                        </div>
                        ${qty > 1 ? `<div class="nota-harga-satuan">${rp(hargaSatuan)}</div>` : ''}
                    `;
                }

                el.innerHTML = `
                    <div class="nota-center">
                        <div class="nota-bold" style="font-size:13px;letter-spacing:1px;">AYAM BAKAR ULAM SARI</div>
                        <div>Graha DMP, Jl. Stadion, Kemiri, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61234</div>
                        <div>+62 0812-5996-2277</div>
                    </div>
                    <hr class="nota-divider-solid">
                    <div class="nota-row"><span>Order No</span><span class="nota-bold">${order.order_number || '#' + order.id}</span></div>
                    <div class="nota-row"><span>Waktu</span><span>${dt}</span></div>
                    <div class="nota-row"><span>Meja</span><span>${mejaTxt}</span></div>
                    <div class="nota-row"><span>Kasir</span><span>Staff</span></div>
                    <hr class="nota-divider-dashed">

                    {{-- ★ TAMBAHAN 3: INFO PELANGGAN DI KERTAS NOTA THERMAL ★ --}}
                    <div class="nota-row"><span>Pemesan</span><span class="nota-bold">${order.customer_name ? order.customer_name.toUpperCase() : 'TANPA NAMA'}</span></div>
                    <div class="nota-row"><span>No. HP</span><span>${order.phone_number ? order.phone_number : '-'}</span></div>
                    <hr class="nota-divider-solid">
                    <div style="margin:5px 0;">
                        ${itemsHTML}
                    </div>
                    <hr class="nota-divider-dashed">
                    <div class="nota-row" style="font-size:13px; margin-top:5px;">
                        <span>TOTAL AKHIR</span>
                        <span class="nota-bold">${rp(order.total_price)}</span>
                    </div>
                    <div class="nota-row">
                        <span>METODE BAYAR</span>
                        <span class="nota-bold" style="text-transform:uppercase;">${order.payment_method}</span>
                    </div>
                    <hr class="nota-divider-solid">
                    <div class="nota-center" style="margin-top:10px; font-weight:bold;">TERIMA KASIH</div>
                    <div class="nota-center" style="margin-top:2px;">Vibe Coding System Active</div>
                `;
            } catch (err) {
                console.error(err);
                el.innerHTML = '<p style="text-align:center;padding:20px;color:red;font-family:sans-serif;">Gagal memuat nota.</p>';
            }
        }

        // (RADAR AUTO REFRESH SETIAP 5 DETIK)
        setInterval(() => {
            fetch('/kasir/api/pending-orders')
                .then(response => {
                    if (!response.ok) throw new Error("Server error " + response.status);
                    return response.json();
                })
                .then(data => {
                    pendingOrders = data;

                    for(let i = 1; i <= 12; i++) {
                        const btnMeja = document.getElementById('btn-meja-' + i);
                        if(!btnMeja) continue;
                        
                        const adaPesanan = data[i] && Object.keys(data[i]).length > 0;
                        let titikMerah = btnMeja.querySelector('.indicator-dot');
                        
                        if(adaPesanan && !titikMerah) {
                            btnMeja.insertAdjacentHTML('beforeend', '<span class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full animate-pulse indicator-dot"></span>');
                        } else if (!adaPesanan && titikMerah) {
                            titikMerah.remove();
                        }
                    }

                    const orderPanel = document.getElementById('panel-order');
                    if (!orderPanel.classList.contains('hidden')) {
                        loadOrderPanel();
                    }
                })
                .catch(err => console.error("RADAR ERROR/TERPUTUS: ", err));
        }, 5000);
    </script>
</body>
</html>