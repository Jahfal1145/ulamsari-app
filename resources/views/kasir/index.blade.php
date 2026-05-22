<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        body { transition: background-color 0.3s, color 0.3s; }

        /* 🔥 SCROLLBAR KHUSUS UNTUK PANEL KANAN 🔥 */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #fb923c; border-radius: 10px; } /* Warna Orange */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #ea580c; } /* Orange Gelap */
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #f97316; }

        @media print {
            body * { visibility: hidden !important; }
            #nota-printable, #nota-printable * { visibility: visible !important; }
            #nota-printable { position: fixed !important; top: 0 !important; left: 0 !important; width: 80mm !important; padding: 0 !important; margin: 0 !important; }
        }
        #nota-printable { font-family: 'Courier New', Courier, monospace; font-size: 11px; line-height: 1.6; color: #000; background: #fff; width: 270px; padding: 6px; }
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
        <div id="alert-error" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-3 rounded-2xl font-bold shadow-2xl animate-bounce">{{ session('error') }}</div>
        <script>setTimeout(() => document.getElementById('alert-error').remove(), 3000);</script>
    @endif
    @if(session('success'))
        <div id="alert-success" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-black text-white px-6 py-3 rounded-2xl font-bold shadow-2xl border-l-8 border-green-500">{{ session('success') }}</div>
        <script>setTimeout(() => document.getElementById('alert-success').remove(), 3000);</script>
    @endif

    <form action="{{ route('kasir.store') }}" method="POST" id="orderForm" class="flex h-screen overflow-hidden">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">
        <input type="hidden" name="customer_name" id="customer_name_input">
        <input type="hidden" name="phone_number" id="phone_number_input">
        <input type="hidden" name="payment_type" id="payment_type">
        <input type="hidden" name="payment_method" id="payment_method" value="Belum Bayar">

        {{-- LEFT PANEL --}}
        <div class="w-3/5 p-6 overflow-y-auto custom-scrollbar flex flex-col relative border-r dark:border-gray-700">
            <div class="flex justify-between items-center mb-6 flex-shrink-0">
                <h2 class="text-3xl font-bold text-orange-600 tracking-tight uppercase">KASIR - ULAM SARI</h2>
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu..." class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl focus:border-orange-500 outline-none font-semibold shadow-sm">
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
                @php 
                    $catName = $menu->categories->count() > 0 ? $menu->categories->first()->name : 'Tanpa Kategori'; 
                    // JURUS ULTIMATE: Ubah Varian jadi Base64 agar 1000% aman dari kutipan HTML!
                    $variantsJson = $menu->variants ? $menu->variants->toJson() : '[]';
                    $variantsBase64 = base64_encode($variantsJson);
                @endphp
                <div onclick="openMenu(this)"
                    class="menu-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 overflow-hidden transition hover:shadow-xl hover:border-orange-400 flex flex-col h-full cursor-pointer group"
                    data-id="{{ $menu->id }}"
                    data-name="{{ $menu->name }}"
                    data-search="{{ strtolower($menu->name) }}"
                    data-price="{{ $menu->price }}"
                    data-category="{{ $catName }}"
                    data-variants="{{ $variantsBase64 }}">
                    
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

        {{-- RIGHT PANEL --}}
        <div class="w-2/5 bg-white dark:bg-gray-800 p-6 shadow-2xl flex flex-col border-l dark:border-gray-700">
            <div class="mb-4 border-b-2 border-gray-100 dark:border-gray-700 pb-4">
                <input type="hidden" name="table_id" id="selected_table_id">
                <button type="button" onclick="openTableModal()" class="w-full bg-white dark:bg-gray-800 text-black dark:text-white border-2 border-gray-100 dark:border-gray-700 p-4 rounded-2xl font-bold text-xl hover:border-orange-500 transition flex justify-center items-center shadow-sm relative group">
                    <span id="table_label" class="uppercase">Nomor Meja</span>
                </button>
            </div>

            <div id="panel-tabs" class="flex gap-2 mb-4">
                <button type="button" onclick="switchPanel('cart')" id="tab-cart" class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white transition">Pesanan Baru</button>
                <button type="button" onclick="switchPanel('order')" id="tab-order" class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition">Cek Meja</button>
                <button type="button" onclick="switchPanel('history')" id="tab-history" class="flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400 transition">Riwayat</button>
            </div>

            <div id="panel-cart" class="flex flex-col flex-1 overflow-hidden">
                {{-- CLASS custom-scrollbar DITAMBAHKAN DI SINI --}}
                <div id="cart-container" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3">
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

            <div id="panel-order" class="flex-col flex-1 overflow-hidden hidden">
                {{-- CLASS custom-scrollbar DITAMBAHKAN DI SINI --}}
                <div id="order-container" class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3"></div>
                <div class="border-t-2 border-gray-100 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-lg uppercase font-bold">Total Meja</span>
                        <span id="order-total-price" class="text-orange-600 text-3xl font-bold">Rp 0</span>
                    </div>
                </div>
            </div>

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
                
                {{-- CLASS custom-scrollbar DITAMBAHKAN DI SINI --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-3">
                    @forelse($historyOrders ?? [] as $history)
                        <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 shadow-sm hover:border-green-400 transition">
                            <div class="flex justify-between items-start mb-2 border-b dark:border-gray-700 pb-2">
                                <div>
                                    <h4 class="font-bold text-black dark:text-white uppercase">{{ $history->order_number }}</h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">{{ $history->created_at->format('d/m/Y H:i') }} • {{ $history->table_id == '0' ? 'TAKEAWAY' : 'MEJA ' . $history->table_id }}</p>
                                </div>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $history->payment_method }}</span>
                            </div>
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

    {{-- MODAL CHECKOUT / PEMBAYARAN --}}
    <div id="paymentModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl p-8">
            <h3 class="text-2xl font-black text-center mb-6 uppercase dark:text-white">Data Pembeli</h3>
            
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Pelanggan <span class="text-red-500">*</span></label>
                    <input type="text" id="modal_customer_name" required placeholder="Nama wajib diisi" class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none uppercase font-bold">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">No. WhatsApp (Opsional)</label>
                    <input type="text" id="modal_phone_number" placeholder="Contoh: 0812345678" class="w-full p-3 border rounded-xl dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
            </div>

            <div id="btn-bayar-langsung" class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button type="button" id="btn-method-tunai" onclick="selectPaymentMethod('Tunai')" class="p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition">💵 Tunai</button>
                    <button type="button" id="btn-method-qris" onclick="selectPaymentMethod('QRIS')" class="p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition">📱 QRIS</button>
                </div>
                <button type="button" onclick="processPayment()" class="w-full bg-green-500 hover:bg-green-600 text-white p-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg transition active:scale-95">💳 BAYAR SEKARANG</button>
            </div>
            
            <div id="btn-bayar-nanti" class="mb-6 hidden">
                <button type="button" onclick="submitFinal('Belum Bayar')" class="w-full bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg transition active:scale-95">Kirim ke Dapur (Bayar Nanti)</button>
            </div>

            <button type="button" onclick="closePaymentModal()" class="w-full text-gray-400 font-bold uppercase text-xs transition hover:text-red-500">Batal</button>
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

    {{-- MODAL TAMBAH ITEM --}}
    <div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-sm rounded-3xl p-6">
            <h3 id="modalName" class="text-xl font-bold mb-1 dark:text-white">Nama Menu</h3>
            <p id="modalPrice" class="text-orange-500 font-bold mb-4">Rp 0</p>
            <input type="hidden" id="modalItemId">

            <div id="variant-container" class="mb-4 hidden border-t-2 border-gray-100 dark:border-gray-700 pt-4">
                </div>

            <div class="mb-4 border-t-2 border-gray-100 dark:border-gray-700 pt-4">
                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Opsi Penyajian Item</label>
                <div id="opsi-penyajian-grid" class="grid grid-cols-2 gap-3">
                    <button type="button" id="btn-item-dine" onclick="selectItemType('Dine In')" class="py-2.5 border-2 rounded-xl font-bold text-xs transition">🍽 Dine In</button>
                    <button type="button" id="btn-item-takeaway" onclick="selectItemType('Takeaway')" class="py-2.5 border-2 rounded-xl font-bold text-xs transition">🛍 Bungkus</button>
                </div>
            </div>

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

    {{-- MODAL PRINT NOTA (THERMAL) --}}
    <div id="printModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 w-full max-w-sm">
            <div class="w-full flex justify-between items-center mb-4">
                <h3 class="text-lg font-black uppercase dark:text-white">Preview Nota</h3>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-red-500 font-bold text-xl">✕</button>
            </div>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 mb-4 w-full flex justify-center bg-gray-50 overflow-auto custom-scrollbar" style="max-height: 300px;">
                <div id="nota-printable-container">
                    <div id="nota-printable"><p style="text-align:center;padding:20px;color:#999;font-family:sans-serif;">Memuat nota...</p></div>
                </div>
            </div>
            <div class="flex gap-3 w-full">
                <button onclick="closePrintModal()" class="flex-1 py-3 border-2 border-gray-200 rounded-2xl font-bold text-gray-500 hover:border-gray-400 transition text-sm">Tutup</button>
                <button onclick="window.print()" class="flex-1 py-3 bg-black text-white rounded-2xl font-bold hover:bg-gray-800 transition text-sm flex items-center justify-center gap-2">Cetak Nota</button>
            </div>
        </div>
    </div>


<script>
   let cart = [];
let pendingOrders = @json($pendingOrders ?? []);
let activePanel = 'cart';
let selectedPaymentMethod = null;
let selectedItemType = 'Dine In'; 
let selectedVariantName = '';
let selectedVariantPrice = 0;
let basePrice = 0;

const formatRupiah = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

function switchPanel(panel) {
    activePanel = panel;
    if(panel === 'cart') {
        document.getElementById('panel-cart').classList.replace('hidden', 'flex');
    } else {
        document.getElementById('panel-cart').classList.replace('flex', 'hidden');
    }
    if(panel === 'order') {
        document.getElementById('panel-order').classList.replace('hidden', 'flex');
    } else {
        document.getElementById('panel-order').classList.replace('flex', 'hidden');
    }
    if(panel === 'history') {
        document.getElementById('panel-history').classList.replace('hidden', 'flex');
    } else {
        document.getElementById('panel-history').classList.replace('flex', 'hidden');
    }
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
    document.querySelectorAll('.meja-option').forEach(el => { el.classList.remove('border-orange-500'); el.classList.add('border-gray-100'); });
    document.getElementById('btn-takeaway-ui').classList.remove('border-orange-500');
    document.getElementById('btn-meja-' + id).classList.remove('border-gray-100');
    document.getElementById('btn-meja-' + id).classList.add('border-orange-500');
    closeTableModal();
    loadOrderPanel();
}

function selectTakeaway() {
    document.getElementById('selected_table_id').value = '0';
    document.getElementById('table_label').innerText = 'TAKEAWAY';
    document.querySelectorAll('.meja-option').forEach(el => { el.classList.remove('border-orange-500'); el.classList.add('border-gray-100'); });
    document.getElementById('btn-takeaway-ui').classList.add('border-orange-500');
    if (cart.length > 0) {
        cart.forEach(item => {
            if (!item.notes.toUpperCase().includes('BUNGKUS') && !item.notes.toUpperCase().includes('TAKEAWAY')) {
                item.notes = (item.notes === '-' || item.notes === '') ? 'Bungkus' : item.notes + ' (Bungkus)';
            }
        });
        updateCartUI();
    }
    closeTableModal();
    loadOrderPanel();
}

function accPesanan(id) {
    if(confirm("Apakah pelanggan sudah membayar? Pesanan akan berubah menjadi Lunas.")) {
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

        if (ord.payment_method === 'Belum Bayar') {
            statusBadge = `<span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase animate-pulse">Belum Bayar</span>`;
            btnKonfirmasi = `
            <div class="mt-3 border-t border-orange-200 dark:border-orange-800 pt-3 space-y-2">
                <button type="button" onclick="accPesanan(${ord.id})" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95">💳 TERIMA UANG (LUNAS)</button>
                <button type="button" onclick="openPrintModal(${ord.id})" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95 flex items-center justify-center gap-2">Cetak Nota</button>
            </div>`;
        } else {
            statusBadge = `<span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded uppercase">Lunas (Sedang Diproses)</span>`;
            btnKonfirmasi = `
            <div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-3">
                <button type="button" onclick="openPrintModal(${ord.id})" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95 flex items-center justify-center gap-2">Cetak Nota</button>
            </div>`;
        }

        container.insertAdjacentHTML('beforeend', `
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-2xl p-4 mt-2 mb-4 shadow-sm">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-bold text-orange-600 uppercase">Pesanan #${list.length - idx}<br><span class="text-gray-500 text-[10px]">${ord.order_number}</span></p>
                    ${statusBadge}
                </div>
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

function openMenu(element) {
    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');
    const price = parseInt(element.getAttribute('data-price'));
    const variantsBase64 = element.getAttribute('data-variants');
    

    document.getElementById('modalQty').value = 1;
    document.getElementById('modalItemId').value = id;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText = formatRupiah(price);
    document.getElementById('modalPrice').dataset.rawPrice = price;
    document.getElementById('modalNotes').value = '';

    basePrice = price;
    selectedVariantName = '';
    selectedVariantPrice = 0;

    let variantsArray = [];
    if (variantsBase64) {
        try {
            const decodedJson = atob(variantsBase64);
            variantsArray = JSON.parse(decodedJson);
        } catch(e) {
            console.error("Gagal decode varian:", e);
        }
    }

    const variantContainer = document.getElementById('variant-container');

    if (variantsArray && variantsArray.length > 0) {
        let html = '';

        variantsArray.forEach((variantGroup) => {
            const variantTitle = variantGroup.name || variantGroup.variant_name || variantGroup.nama || 'Varian';
            const options = variantGroup.options || variantGroup.values || variantGroup.items || [];

            html += `<div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">${variantTitle}</label>
                <div class="grid grid-cols-2 gap-2">`;

            options.forEach((option, index) => {
                const optionName = typeof option === 'string' ? option : (option.name || option.nama || 'Opsi');
                html += `
                    <label class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-2 flex items-center gap-2 cursor-pointer hover:border-orange-500 transition">
                        <input type="radio" name="varian_${variantTitle}" value="${optionName}"
                            onchange='pilihVarian("${optionName}", 0)'
                            class="accent-orange-500" ${index === 0 ? 'checked' : ''}>
                        <span class="text-xs font-bold dark:text-white">${optionName}</span>
                    </label>`;
            });

            html += `</div></div>`;
        });

        variantContainer.innerHTML = html;
        variantContainer.classList.remove('hidden');

        if (variantsArray[0]?.options?.length > 0) {
            pilihVarian(variantsArray[0].options[0], 0);
        }
    } else {
        variantContainer.innerHTML = '';
        variantContainer.classList.add('hidden');
    }

    const currentTable = document.getElementById('selected_table_id').value;
    const gridPenyajian = document.getElementById('opsi-penyajian-grid');
    const btnDine = document.getElementById('btn-item-dine');

    if (currentTable === '0') {
        gridPenyajian.classList.remove('grid-cols-2');
        gridPenyajian.classList.add('grid-cols-1');
        btnDine.classList.add('hidden');
        selectItemType('Takeaway');
    } else {
        gridPenyajian.classList.remove('grid-cols-1');
        gridPenyajian.classList.add('grid-cols-2');
        btnDine.classList.remove('hidden');
        selectItemType('Dine In');
    }

    document.getElementById('addModal').classList.replace('hidden', 'flex');
}

function pilihVarian(nama, hargaTambahan) {
    selectedVariantName = nama;
    selectedVariantPrice = parseInt(hargaTambahan) || 0;
    const totalItemPrice = basePrice + selectedVariantPrice;
    document.getElementById('modalPrice').innerText = formatRupiah(totalItemPrice);
    document.getElementById('modalPrice').dataset.rawPrice = totalItemPrice;
}

function selectItemType(type) {
    selectedItemType = type;
    const btnDine = document.getElementById('btn-item-dine');
    const btnTakeaway = document.getElementById('btn-item-takeaway');

    if(type === 'Dine In') {
        btnDine.className = "py-2.5 border-2 border-orange-500 bg-orange-50 dark:bg-orange-900/20 rounded-xl font-bold text-xs text-orange-600 dark:text-orange-400 text-center transition";
        btnTakeaway.className = "py-2.5 border-2 border-gray-100 dark:border-gray-700 rounded-xl font-bold text-xs text-gray-500 dark:text-gray-400 text-center transition";
    } else {
        btnDine.className = "py-2.5 border-2 border-gray-100 dark:border-gray-700 rounded-xl font-bold text-xs text-gray-500 dark:text-gray-400 text-center transition";
        btnTakeaway.className = "py-2.5 border-2 border-red-500 bg-red-50 dark:bg-red-950/20 rounded-xl font-bold text-xs text-red-600 dark:text-red-400 text-center transition";
    }
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

    const currentTable = document.getElementById('selected_table_id').value;
    let finalNotes = notes;
    if (currentTable === '0' || selectedItemType === 'Takeaway') {
        if (!finalNotes.toUpperCase().includes('BUNGKUS') && !finalNotes.toUpperCase().includes('TAKEAWAY')) {
            finalNotes = (finalNotes === '-' || finalNotes === '') ? 'Bungkus' : finalNotes + ' (Bungkus)';
        }
    }

    cart.push({
        menu_id: id,
        name: name,
        variant: selectedVariantName || '',
        price: price,
        qty: qty,
        subtotal: price * qty,
        notes: finalNotes
    });

    updateCartUI();
    closeAddModal();
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

        const isBungkus = item.notes.toUpperCase().includes('BUNGKUS') || item.notes.toUpperCase().includes('TAKEAWAY');
        const penyajianBadge = isBungkus
            ? `<span class="bg-red-100 text-red-700 text-[9px] px-2 py-0.5 rounded-md font-bold uppercase">Bungkus</span>`
            : `<span class="bg-blue-100 text-blue-700 text-[9px] px-2 py-0.5 rounded-md font-bold uppercase">Dine In</span>`;

        let textCatatan = item.notes.replace(/ \(Bungkus\)/gi, '').replace(/Bungkus/gi, '').trim();

        container.insertAdjacentHTML('beforeend', `
            <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-2xl p-4 shadow-sm flex flex-col gap-2 relative group hover:border-orange-500 transition">
                <div class="flex justify-between items-start pr-8">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex flex-col">
                                <h4 class="font-bold text-sm leading-tight dark:text-white uppercase">${item.name}</h4>
                                ${item.variant ? `<span class="text-[10px] text-orange-500 font-bold uppercase tracking-wide">${item.variant}</span>` : ''}
                            </div>
                            ${penyajianBadge}
                        </div>
                        <p class="text-orange-500 font-bold text-sm mt-1">${formatRupiah(item.price)}</p>
                    </div>
                    <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-xl text-sm font-black dark:text-white">x${item.qty}</span>
                </div>
                ${(textCatatan !== '-' && textCatatan !== '') ? `<div class="bg-orange-50 dark:bg-orange-900/20 text-orange-600 px-3 py-2 rounded-xl text-xs font-semibold">Catatan: ${textCatatan}</div>` : ''}
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

    selectedPaymentMethod = null;
    const btnTunai = document.getElementById('btn-method-tunai');
    const btnQris = document.getElementById('btn-method-qris');
    if(btnTunai && btnQris) {
        btnTunai.className = "p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition";
        btnQris.className = "p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition";
    }

    if (type === 'later') {
        document.getElementById('btn-bayar-langsung').classList.add('hidden');
        document.getElementById('btn-bayar-nanti').classList.remove('hidden');
    } else {
        document.getElementById('btn-bayar-langsung').classList.remove('hidden');
        document.getElementById('btn-bayar-nanti').classList.add('hidden');
    }

    document.getElementById('paymentModal').classList.replace('hidden', 'flex');
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    const btnTunai = document.getElementById('btn-method-tunai');
    const btnQris = document.getElementById('btn-method-qris');

    btnTunai.className = "p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition";
    btnQris.className = "p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-center hover:border-orange-500 transition";
}
</script>
</body>
</html>