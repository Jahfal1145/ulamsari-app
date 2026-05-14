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
                <h2 class="text-3xl font-bold text-orange-600 tracking-tight uppercase">Pilih Menu</h2>
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
    <div id="paymentModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl p-8">
            <h3 class="text-2xl font-black text-center mb-6 uppercase dark:text-white">Pilih Pembayaran</h3>
            <div class="grid grid-cols-1 gap-3 mb-6">
                <button type="button" onclick="submitFinal('Tunai')" class="p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-left hover:border-orange-500 transition">Tunai / Cash</button>
                <button type="button" onclick="submitFinal('QRIS')" class="p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-left hover:border-orange-500 transition">QRIS</button>
                <button type="button" onclick="submitFinal('Transfer')" class="p-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white text-left hover:border-orange-500 transition">Transfer Bank</button>
            </div>
            <button type="button" onclick="closePaymentModal()" class="w-full text-gray-400 font-bold uppercase text-xs transition">Batal</button>
        </div>
    </div>

    {{-- TABLE MODAL --}}
    <div id="tableModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-3xl p-8">
            <h2 class="text-2xl font-bold text-center mb-6 uppercase dark:text-white">Denah Meja</h2>
            <div class="grid grid-cols-4 gap-3 mb-6">
                @for ($i = 1; $i <= 12; $i++)
                    @php $hasOrder = isset($pendingOrders[$i]) && count($pendingOrders[$i]) > 0; @endphp
                    <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}" class="meja-option aspect-square flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 font-bold relative hover:border-orange-500 transition">
                        <span class="text-[10px] text-gray-400">MEJA</span>
                        <span class="text-xl dark:text-white">{{ $i }}</span>
                        @if($hasOrder) <span class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span> @endif
                    </button>
                @endfor
            </div>
            <button type="button" onclick="selectTakeaway()" id="btn-takeaway-ui" class="w-full mb-4 py-4 border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold dark:text-white hover:border-orange-500 transition">TAKEAWAY</button>
            <div id="takeaway-input-container" class="hidden mb-4"><input type="text" id="takeaway_name_field" onkeyup="syncCustomerName()" placeholder="Nama Pelanggan..." class="w-full p-4 border-2 border-orange-500 rounded-xl font-bold outline-none"></div>
            <button type="button" onclick="closeTableModal()" class="w-full bg-black text-white py-3 rounded-xl font-bold transition active:scale-95">Tutup</button>
        </div>
    </div>

    {{-- ITEM MODAL --}}
    <div id="itemModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 w-[420px] rounded-3xl p-8">
            <div class="flex justify-between items-start mb-6 border-b dark:border-gray-700 pb-4">
                <div><h2 id="modalItemName" class="text-2xl font-bold dark:text-white">Nama Item</h2><p id="modalItemPrice" class="text-orange-500 font-bold">Rp 0</p></div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 font-bold">X</button>
            </div>
            <input type="hidden" id="modalItemId">
            <div class="space-y-4">
                <div>
                    <label class="block font-bold text-gray-600 dark:text-gray-400 text-sm mb-2">Jumlah</label>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-700 p-2 rounded-2xl w-fit">
                        <button type="button" onclick="changeQty(-1)" class="w-10 h-10 font-black dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition">-</button>
                        <input type="number" id="modalQty" value="1" class="w-12 text-center font-bold bg-transparent outline-none dark:text-white" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-10 h-10 font-black dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition">+</button>
                    </div>
                </div>
                <div class="pt-4 border-t dark:border-gray-700 flex gap-4">
                    <label id="label-dinein" class="flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition"><input type="radio" name="orderType" value="Dine In" class="hidden" checked onchange="toggleTypeUI()"> Dine In</label>
                    <label id="label-takeaway" class="flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition"><input type="radio" name="orderType" value="Takeaway" class="hidden" onchange="toggleTypeUI()"> Takeaway</label>
                </div>
                <button type="button" onclick="saveToCart()" class="w-full bg-black text-white py-4 rounded-2xl font-bold shadow-lg hover:scale-105 transition active:scale-95">Tambahkan</button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT UTAMA --}}
    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
        let pendingOrders = @json($pendingOrders ?? []);

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
        }

        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }

        function selectTable(num) {
            document.getElementById('selected_table_id').value = num;
            document.getElementById('table_label').innerText = "MEJA " + num;
            document.getElementById('takeaway-input-container').classList.add('hidden');
            const tabs = document.getElementById('panel-tabs'); tabs.classList.remove('hidden'); tabs.classList.add('flex');
            switchPanel(pendingOrders[num] ? 'order' : 'cart');
            closeTableModal();
        }

        function selectTakeaway() {
            document.getElementById('selected_table_id').value = "0";
            document.getElementById('table_label').innerText = "TAKEAWAY";
            document.getElementById('takeaway-input-container').classList.remove('hidden');
            switchPanel('cart');
        }

        function syncCustomerName() { document.getElementById('customer_name_input').value = document.getElementById('takeaway_name_field').value; }

        function switchPanel(p) {
            const isCart = p === 'cart', isOrder = p === 'order', isHistory = p === 'history';
            document.getElementById('panel-cart').classList.toggle('hidden', !isCart);
            document.getElementById('panel-cart').classList.toggle('flex', isCart);
            document.getElementById('panel-order').classList.toggle('hidden', !isOrder);
            document.getElementById('panel-order').classList.toggle('flex', isOrder);
            document.getElementById('panel-history').classList.toggle('hidden', !isHistory);
            document.getElementById('panel-history').classList.toggle('flex', isHistory);
            
            const activeClass = 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-orange-500 bg-orange-500 text-white';
            const inactiveClass = 'flex-1 py-2 rounded-xl font-bold text-[11px] uppercase border-2 border-gray-100 dark:border-gray-700 text-gray-400';
            document.getElementById('tab-cart').className = isCart ? activeClass : inactiveClass;
            document.getElementById('tab-order').className = isOrder ? activeClass : inactiveClass;
            document.getElementById('tab-history').className = isHistory ? activeClass : inactiveClass;

            if (isOrder) loadOrderPanel();
        }

        function loadOrderPanel() {
            const tableId = document.getElementById('selected_table_id').value;
            const container = document.getElementById('order-container');
            const totalEl = document.getElementById('order-total-price');
            const rawOrder = pendingOrders[tableId];

            if (!rawOrder || (Array.isArray(rawOrder) && rawOrder.length === 0)) {
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
                
                // MUNCULKAN TOMBOL ACC (TIPE BUTTON BIASA, BUKAN FORM)
                if (ord.order_status_id == 4) {
                    statusBadge = `<span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded uppercase animate-pulse">Belum Bayar</span>`;
                    btnKonfirmasi = `
                    <div class="mt-3 border-t border-orange-200 dark:border-orange-800 pt-3">
                        <button type="button" onclick="accPesanan(${ord.id})" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl text-xs uppercase shadow-md transition active:scale-95">
                            Terima Uang & ACC ke Dapur
                        </button>
                    </div>`;
                } else {
                    statusBadge = `<span class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-1 rounded uppercase">Proses Dapur</span>`;
                }

                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-2xl p-4 mt-2 mb-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs font-bold text-orange-600 uppercase">Pesanan #${list.length - idx} <br><span class="text-gray-500 text-[10px]">${ord.order_number}</span></p>
                            ${statusBadge}
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
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;
            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function closeModal() { document.getElementById('itemModal').classList.replace('flex', 'hidden'); }
        function changeQty(v) { const q = document.getElementById('modalQty'); if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v; }

        function toggleTypeUI() {
            const isDineIn = document.querySelector('input[name="orderType"]:checked').value === 'Dine In';
            document.getElementById('label-dinein').className = isDineIn ? 'flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition' : 'flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition';
            document.getElementById('label-takeaway').className = !isDineIn ? 'flex-1 border-2 border-orange-500 bg-orange-50 text-orange-600 p-3 rounded-xl text-center font-bold cursor-pointer transition' : 'flex-1 border-2 border-gray-100 text-gray-500 p-3 rounded-xl text-center font-bold cursor-pointer transition';
        }

        function saveToCart() {
            const id = document.getElementById('modalItemId').value;
            const name = document.getElementById('modalItemName').innerText;
            const price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice);
            const qty = parseInt(document.getElementById('modalQty').value);
            const type = document.querySelector('input[name="orderType"]:checked').value;

            cart.unshift({ menu_id: id, name, price, qty, subtotal: price * qty, notes: type });
            closeModal();
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cart-container');
            const totalEl = document.getElementById('total-price');
            container.innerHTML = ''; let total = 0;

            if (cart.length === 0) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-300 italic font-bold"><p>BELUM ADA MENU DIPILIH</p></div>`;
                totalEl.innerText = 'Rp 0';
                document.getElementById('cart_data_input').value = '[]';
                return;
            }

            cart.forEach((item, i) => {
                total += item.subtotal;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-xl p-4 flex justify-between items-center shadow-sm">
                        <div><h4 class="font-bold dark:text-white uppercase">${item.name}</h4><p class="text-xs font-bold text-orange-500 mt-1">${item.notes}</p></div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold dark:text-white">${formatRupiah(item.subtotal)}</span>
                            <button type="button" onclick="removeItem(${i})" class="text-red-500 hover:scale-110 transition font-bold">X</button>
                        </div>
                    </div>
                `);
            });

            totalEl.innerText = formatRupiah(total);
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
        }

        function removeItem(i) { cart.splice(i, 1); updateCartUI(); }

        function validateAndSubmit(type) {
            if (!document.getElementById('selected_table_id').value) { alert("Pilih Meja dulu rek!"); openTableModal(); return; }
            if (cart.length === 0) { alert("Keranjang kosong!"); return; }
            document.getElementById('payment_type').value = type;
            if (type === 'now') { document.getElementById('paymentModal').classList.replace('hidden', 'flex'); } 
            else { document.getElementById('payment_method').value = "Belum Bayar"; document.getElementById('orderForm').submit(); }
        }

        function closePaymentModal() { document.getElementById('paymentModal').classList.replace('flex', 'hidden'); }
        function submitFinal(method) { document.getElementById('payment_method').value = method; document.getElementById('orderForm').submit(); }
        
        function searchMenu() { const val = document.getElementById('searchInput').value.toLowerCase(); document.querySelectorAll('.menu-card').forEach(c => c.style.display = c.dataset.name.includes(val) ? 'flex' : 'none'); }
        function filterMenu(k) { document.querySelectorAll('.menu-card').forEach(c => c.style.display = (k === 'semua' || c.dataset.category === k) ? 'flex' : 'none'); }
        
        function exportExcel() {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            if (!start || !end) { alert('Pilih tanggal Mulai & Selesai dulu!'); return; }
            window.location.href = "{{ route('kasir.export') }}?start_date=" + start + "&end_date=" + end;
        }
    </script>

    {{-- FORM RAHASIA UNTUK ACC PESANAN (DI LUAR FORM UTAMA) --}}
    <form id="formKonfirmasiRahasia" method="POST" class="hidden">
        @csrf
    </form>
    
    <script>
        // FUNGSI UNTUK MENEKAN TOMBOL ACC MENGGUNAKAN FORM RAHASIA
        function accPesanan(orderId) {
            const formACC = document.getElementById('formKonfirmasiRahasia');
            formACC.action = `/kasir/konfirmasi/${orderId}`;
            formACC.submit();
        }

        // ==========================================
        // RADAR OTOMATIS (AJAX POLLING SETIAP 5 DETIK)
        // ==========================================
        setInterval(() => {
            fetch('/kasir/api/pending-orders')
                .then(response => {
                    if (!response.ok) throw new Error("Server error " + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("RADAR BERHASIL CATCH DATA BARU: ", data); // <--- CCTV kita
                    
                    // 1. Update data di memori JavaScript
                    pendingOrders = data;

                    // 2. Update titik merah berkedip di Denah Meja
                    for(let i = 1; i <= 12; i++) {
                        const btnMeja = document.getElementById('btn-meja-' + i);
                        if(!btnMeja) continue;
                        
                        // Cek apakah ada pesanan di meja i
                        const adaPesanan = data[i] && Object.keys(data[i]).length > 0;
                        let titikMerah = btnMeja.querySelector('.indicator-dot');
                        
                        if(adaPesanan && !titikMerah) {
                            // Munculkan titik merah kalau ada pesanan baru
                            btnMeja.insertAdjacentHTML('beforeend', '<span class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full animate-pulse indicator-dot"></span>');
                        } else if (!adaPesanan && titikMerah) {
                            // Hilangkan titik merah kalau pesanan sudah beres
                            titikMerah.remove();
                        }
                    }

                    // 3. Jika kasir sedang membuka layar "Cek Meja", otomatis refresh tampilannya!
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