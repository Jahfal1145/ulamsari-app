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
        /* Animasi floating cart */
        #floating-cart { transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-100 relative overflow-hidden">

    @if(session('error'))
        <div id="alert-error" class="fixed top-5 left-1/2 -translate-x-1/2 z- bg-red-600 text-white px-6 py-3 rounded-2xl font-bold shadow-2xl animate-bounce">
            {{ session('error') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-error').remove(), 3000);</script>
    @endif
    
    @if(session('success'))
        <div id="alert-success" class="fixed top-5 left-1/2 -translate-x-1/2 z- bg-black text-white px-6 py-3 rounded-2xl font-bold shadow-2xl border-l-8 border-orange-500">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-success').remove(), 3000);</script>
    @endif

    <form action="{{ route('kasir.store') }}" method="POST" id="orderForm" class="flex h-screen overflow-hidden">
        @csrf
        <input type="hidden" name="cart_data" id="cart_data_input">
        <input type="hidden" name="customer_name" id="customer_name_input">
        <input type="hidden" name="table_id" id="selected_table_id">

        {{-- ===== MAIN PANEL: FULL MENU ===== --}}
        <div class="w-full p-6 overflow-y-auto flex flex-col relative z-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-3xl font-black text-orange-600 tracking-tighter uppercase italic">Ulam Sari <span class="text-gray-400 dark:text-gray-600 font-light">POS</span></h2>
                    <button type="button" onclick="openTableModal()" id="table_label" class="text-lg font-bold text-gray-500 dark:text-gray-400 flex items-center gap-2 hover:text-orange-500 transition">
                        PILIH NOMOR MEJA <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                    </button>
                </div>
                
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu favorit..."
                        class="w-full pl-10 pr-4 py-3 border-2 border-transparent bg-white dark:bg-gray-800 rounded-2xl focus:border-orange-500 outline-none font-bold shadow-sm transition-all">
                </div>
            </div>

            <div class="flex gap-3 mb-8 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-8 py-3 rounded-2xl font-bold shadow-lg transition whitespace-nowrap">Semua Menu</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn bg-white dark:bg-gray-800 text-gray-500 px-8 py-3 rounded-2xl font-bold border-2 border-transparent hover:border-orange-500 transition whitespace-nowrap">Makanan</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn bg-white dark:bg-gray-800 text-gray-500 px-8 py-3 rounded-2xl font-bold border-2 border-transparent hover:border-orange-500 transition whitespace-nowrap">Minuman</button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 pb-40" id="menuGrid">
                @foreach($menus as $menu)
                <div onclick="openAddModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->category_name) }}', '{{ $menu->type }}')"
                    class="menu-card bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border-4 border-transparent overflow-hidden transition-all hover:shadow-2xl hover:border-orange-500 cursor-pointer group flex flex-col h-full"
                    data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    <div class="h-44 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform duration-500">
                        <span class="text-4xl">🍲</span>
                    </div>
                    <div class="p-5 flex flex-col flex-1 relative bg-white dark:bg-gray-800">
                        <h3 class="font-black text-lg leading-tight mb-1 text-gray-800 dark:text-gray-100 uppercase tracking-tight">{{ $menu->name }}</h3>
                        <p class="text-orange-600 font-black text-xl">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ===== FLOATING CART (GOJEK STYLE) ===== --}}
        <div id="floating-cart" class="fixed bottom-8 left-1/2 -translate-x-1/2 z- w-[90%] max-w-lg translate-y-40">
            <div class="bg-black dark:bg-orange-600 text-white p-4 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex justify-between items-center border-4 border-white dark:border-gray-800">
                <div class="flex items-center gap-4 ml-2">
                    <div class="relative bg-white/20 p-3 rounded-2xl">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z" stroke-width="2.5"/></svg>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-black w-6 h-6 flex items-center justify-center rounded-full border-2 border-black">0</span>
                    </div>
                    <div>
                        <p id="floating-total" class="font-black text-2xl leading-none">Rp 0</p>
                        <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest mt-1">Total Pesanan</p>
                    </div>
                </div>
                <button type="button" onclick="toggleCartPanel()" class="bg-white text-black font-black px-6 py-4 rounded-[1.8rem] hover:bg-orange-100 transition active:scale-95 uppercase tracking-tighter">
                    Cek Keranjang
                </button>
            </div>
        </div>

        {{-- ===== SIDE CART PANEL (HIDDEN BY DEFAULT) ===== --}}
        <div id="side-cart" class="fixed inset-y-0 right-0 w-full md:w-[450px] bg-white dark:bg-gray-900 shadow-2xl z- translate-x-full transition-transform duration-500 flex flex-col">
            <div class="p-6 border-b dark:border-gray-800 flex justify-between items-center">
                <h2 class="text-2xl font-black uppercase tracking-tighter italic">Review Pesanan</h2>
                <button type="button" onclick="toggleCartPanel()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                </button>
            </div>
            <div id="cart-container" class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-hide">
                </div>
            <div class="p-6 border-t dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">Total Bayar</p>
                        <h2 id="final-total" class="text-4xl font-black text-orange-600">Rp 0</h2>
                    </div>
                </div>
                <button type="button" onclick="validateAndSubmit()" class="w-full bg-orange-500 text-white py-6 rounded-[2rem] font-black text-2xl hover:bg-black transition-all shadow-xl active:scale-95 uppercase italic tracking-tighter">
                    Kirim Pesanan Sekarang
                </button>
            </div>
        </div>
    </form>

    {{-- ===== ITEM MODAL ===== --}}
    <div id="itemModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z- backdrop-blur-md p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-[420px] rounded-[3rem] shadow-2xl p-8 transform transition-all scale-95 animate-in">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 id="modalItemName" class="text-3xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">Nama Item</h2>
                    <p id="modalItemPrice" class="text-orange-500 font-black text-2xl">Rp 0</p>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 p-2 transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                </button>
            </div>

            <input type="hidden" id="modalItemId">
            <input type="hidden" id="modalEditIndex" value="-1">

            <div class="space-y-6">
                {{-- Opsi Khusus Ayam --}}
                <div id="chickenPartContainer" class="hidden">
                    <label class="block font-black text-gray-400 text-xs mb-2 uppercase tracking-widest italic">Pilih Bagian:</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Bebas','Dada','Paha','Sayap'] as $p)
                        <button type="button" onclick="setSelect('chickenPart', '{{$p}}')" class="opt-chickenPart bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl font-bold dark:text-white border-2 border-transparent transition" data-val="{{$p}}">{{$p}}</button>
                        @endforeach
                        <input type="hidden" id="chickenPart" value="Bebas">
                    </div>
                </div>

                {{-- Opsi Pedas --}}
                <div id="spicyLevelContainer" class="hidden">
                    <label class="block font-black text-gray-400 text-xs mb-2 uppercase tracking-widest italic">Level Pedas:</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Gak Pedas','Sedang','Pedas'] as $l)
                        <button type="button" onclick="setSelect('spicyLevel', '{{$l}}')" class="opt-spicyLevel bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl font-bold dark:text-white border-2 border-transparent transition" data-val="{{$l}}">{{$l}}</button>
                        @endforeach
                        <input type="hidden" id="spicyLevel" value="Gak Pedas">
                    </div>
                </div>

                {{-- Opsi Khusus Air Botolan --}}
                <div id="bottledOptionContainer" class="hidden">
                    <label class="block font-black text-gray-400 text-xs mb-2 uppercase tracking-widest italic">Suhu Air:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="setSelect('bottledTemp', 'Dingin')" class="opt-bottledTemp bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl font-bold dark:text-white border-2 border-transparent transition" data-val="Dingin">Dingin</button>
                        <button type="button" onclick="setSelect('bottledTemp', 'Biasa')" class="opt-bottledTemp bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl font-bold dark:text-white border-2 border-transparent transition" data-val="Biasa">Biasa</button>
                        <input type="hidden" id="bottledTemp" value="Dingin">
                    </div>
                </div>

                {{-- Minuman Custom --}}
                <div id="drinkCustomContainer" class="hidden space-y-4">
                    <div id="iceLevelContainer">
                        <label class="block font-black text-gray-400 text-xs mb-2 uppercase tracking-widest italic">Es:</label>
                        <select id="iceLevel" class="w-full bg-gray-100 dark:bg-gray-700 p-4 rounded-2xl font-bold outline-none border-none">
                            <option>Es Normal</option><option>Less Ice</option><option>Tanpa Es</option>
                        </select>
                    </div>
                    <div id="sugarLevelContainer">
                        <label class="block font-black text-gray-400 text-xs mb-2 uppercase tracking-widest italic">Gula:</label>
                        <select id="sugarLevel" class="w-full bg-gray-100 dark:bg-gray-700 p-4 rounded-2xl font-bold outline-none border-none">
                            <option>Gula Normal</option><option>Less Sugar</option><option>Tanpa Gula</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <label class="font-black text-gray-400 text-xs uppercase tracking-widest italic">Jumlah:</label>
                    <div class="flex items-center gap-6 bg-gray-100 dark:bg-gray-700 p-2 rounded-3xl">
                        <button type="button" onclick="changeQty(-1)" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl shadow-md font-black text-2xl hover:bg-orange-500 hover:text-white transition">-</button>
                        <input type="number" id="modalQty" value="1" class="w-8 text-center font-black text-2xl bg-transparent outline-none" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl shadow-md font-black text-2xl hover:bg-orange-500 hover:text-white transition">+</button>
                    </div>
                </div>

                <div class="flex gap-2 pt-4">
                    <label id="label-dinein" class="flex-1 border-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-600 p-4 rounded-3xl cursor-pointer text-center font-black transition-all">
                        <input type="radio" name="orderType" value="Dine In" class="hidden" checked onchange="toggleOrderTypeUI()"> DINE IN
                    </label>
                    <label id="label-takeaway" class="flex-1 border-4 border-transparent bg-gray-100 dark:bg-gray-700 text-gray-400 p-4 rounded-3xl cursor-pointer text-center font-black transition-all">
                        <input type="radio" name="orderType" value="Takeaway" class="hidden" onchange="toggleOrderTypeUI()"> TAKE AWAY
                    </label>
                </div>

                <button type="button" onclick="saveToCart()" id="btn-submit-modal" class="w-full bg-black dark:bg-orange-600 text-white py-5 rounded-[2rem] font-black text-xl shadow-xl hover:scale-[1.02] transition active:scale-95 uppercase italic tracking-tighter">
                    Masukkan Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- ===== TABLE MODAL ===== --}}
    <div id="tableModal" class="fixed inset-0 bg-black/90 hidden items-center justify-center z- backdrop-blur-xl p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-[3rem] p-10">
            <h2 class="text-3xl font-black text-center mb-8 uppercase italic tracking-tighter">Pilih Lokasi Makan</h2>
            <div class="grid grid-cols-4 gap-4 mb-8">
                @for ($i = 1; $i <= 12; $i++)
                    <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}"
                        class="meja-option aspect-square flex flex-col items-center justify-center rounded-[1.5rem] border-4 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-orange-500 transition-all font-black group relative">
                        <span class="text-[9px] text-gray-400 uppercase">Meja</span>
                        <span class="text-2xl group-hover:text-orange-600">{{ $i }}</span>
                    </button>
                @endfor
            </div>

            <button type="button" onclick="selectTakeaway()" id="btn-takeaway-ui" class="w-full mb-4 flex items-center justify-center gap-3 border-4 border-gray-100 dark:border-gray-700 py-5 rounded-3xl font-black text-xl hover:border-orange-500 transition uppercase italic">
                🥡 Take Away (Bungkus)
            </button>

            <div id="takeaway-input-container" class="hidden mb-6">
                <input type="text" id="takeaway_name_field" onkeyup="syncCustomerName()" placeholder="Nama Pelanggan..." class="w-full p-5 bg-gray-100 dark:bg-gray-700 rounded-2xl outline-none font-black text-xl border-4 border-orange-500">
            </div>

            <button type="button" onclick="closeTableModal()" class="w-full bg-black text-white py-4 rounded-2xl font-black uppercase tracking-widest">Tutup</button>
        </div>
    </div>

    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // =====================
        // CORE FUNCTIONS
        // =====================
        function toggleCartPanel() {
            document.getElementById('side-cart').classList.toggle('translate-x-full');
        }

        function openAddModal(id, name, price, cat, type) {
            resetModal();
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;

            const isAyam = name.toLowerCase().includes('ayam');
            const isMinuman = cat === 'Minuman';
            const isBottled = type === 'bottled';

            document.getElementById('chickenPartContainer').classList.toggle('hidden', !isAyam);
            document.getElementById('spicyLevelContainer').classList.toggle('hidden', isMinuman);
            document.getElementById('bottledOptionContainer').classList.toggle('hidden', !isBottled);
            document.getElementById('drinkCustomContainer').classList.toggle('hidden', !isMinuman || isBottled);

            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function setSelect(id, val) {
            document.getElementById(id).value = val;
            document.querySelectorAll(`.opt-${id}`).forEach(b => {
                const isActive = b.dataset.val === val;
                b.className = `opt-${id} p-3 rounded-2xl font-bold transition border-2 ${isActive ? 'bg-orange-500 text-white border-orange-500 shadow-lg' : 'bg-gray-100 dark:bg-gray-700 dark:text-white border-transparent'}`;
            });
        }

        function resetModal() {
            document.getElementById('modalQty').value = 1;
            document.getElementById('modalEditIndex').value = -1;
            setSelect('chickenPart', 'Bebas');
            setSelect('spicyLevel', 'Gak Pedas');
            setSelect('bottledTemp', 'Dingin');
            document.getElementById('iceLevel').value = 'Es Normal';
            document.getElementById('sugarLevel').value = 'Gula Normal';
        }

        function saveToCart() {
            const id = document.getElementById('modalItemId').value;
            const name = document.getElementById('modalItemName').innerText;
            const price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice);
            const qty = parseInt(document.getElementById('modalQty').value);
            const type = document.querySelector('input[name="orderType"]:checked').value;
            
            let notes = [type];
            // Tambah catatan sesuai filter yang muncul
            if(!document.getElementById('chickenPartContainer').classList.contains('hidden')) notes.push(document.getElementById('chickenPart').value);
            if(!document.getElementById('spicyLevelContainer').classList.contains('hidden')) notes.push(document.getElementById('spicyLevel').value);
            if(!document.getElementById('bottledOptionContainer').classList.contains('hidden')) notes.push(document.getElementById('bottledTemp').value);
            if(!document.getElementById('drinkCustomContainer').classList.contains('hidden')) {
                notes.push(document.getElementById('iceLevel').value);
                notes.push(document.getElementById('sugarLevel').value);
            }

            const noteStr = notes.join(' • ');
            const existing = cart.findIndex(i => i.menu_id === id && i.notes === noteStr);

            if (existing > -1) {
                cart[existing].qty += qty;
                cart[existing].subtotal = cart[existing].qty * price;
            } else {
                cart.unshift({ menu_id: id, name, price, qty, subtotal: price * qty, notes: noteStr });
            }

            document.getElementById('itemModal').classList.replace('flex', 'hidden');
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cart-container');
            const floatTotal = document.getElementById('floating-total');
            const finalTotal = document.getElementById('final-total');
            const cartCount = document.getElementById('cart-count');
            const floatCart = document.getElementById('floating-cart');

            container.innerHTML = '';
            let total = 0;
            let totalQty = 0;

            cart.forEach((item, i) => {
                total += item.subtotal;
                totalQty += item.qty;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-[2rem] border-2 border-transparent hover:border-orange-500 transition relative group">
                        <button type="button" onclick="removeItem(${i})" class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                        </button>
                        <h4 class="font-black uppercase text-sm">${item.name}</h4>
                        <p class="text-[10px] font-bold text-orange-500 uppercase italic mb-3">${item.notes}</p>
                        <div class="flex justify-between items-center">
                            <span class="bg-black text-white px-3 py-1 rounded-full text-xs font-black">x${item.qty}</span>
                            <span class="font-black text-lg">${formatRupiah(item.subtotal)}</span>
                        </div>
                    </div>
                `);
            });

            floatTotal.innerText = formatRupiah(total);
            finalTotal.innerText = formatRupiah(total);
            cartCount.innerText = totalQty;
            document.getElementById('cart_data_input').value = JSON.stringify(cart);

            // Tampilkan/Sembunyikan Floating Cart
            if(cart.length > 0) floatCart.classList.remove('translate-y-40');
            else floatCart.classList.add('translate-y-40');
        }

        function removeItem(i) {
            cart.splice(i, 1);
            updateCartUI();
        }

        // =====================
        // UI HELPERS
        // =====================
        function changeQty(v) {
            const q = document.getElementById('modalQty');
            if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v;
        }

        function closeModal() { document.getElementById('itemModal').classList.replace('flex', 'hidden'); }
        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }

        function selectTable(num) {
            document.getElementById('selected_table_id').value = num;
            document.getElementById('table_label').innerHTML = `<span class="bg-orange-500 text-white px-3 py-1 rounded-lg">MEJA ${num}</span>`;
            document.getElementById('btn-takeaway-ui').className = "w-full mb-4 flex items-center justify-center gap-3 border-4 border-gray-100 dark:border-gray-700 py-5 rounded-3xl font-black text-xl hover:border-orange-500 transition uppercase italic";
            closeTableModal();
        }

        function selectTakeaway() {
            document.getElementById('selected_table_id').value = "0";
            document.getElementById('table_label').innerHTML = `<span class="bg-black text-white px-3 py-1 rounded-lg">TAKE AWAY</span>`;
            document.getElementById('takeaway-input-container').classList.remove('hidden');
            document.getElementById('takeaway_name_field').focus();
        }

        function syncCustomerName() {
            document.getElementById('customer_name_input').value = document.getElementById('takeaway_name_field').value;
        }

        function toggleOrderTypeUI() {
            const type = document.querySelector('input[name="orderType"]:checked').value;
            const isDine = type === 'Dine In';
            document.getElementById('label-dinein').className = isDine ? 'flex-1 border-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-600 p-4 rounded-3xl cursor-pointer text-center font-black transition-all' : 'flex-1 border-4 border-transparent bg-gray-100 dark:bg-gray-700 text-gray-400 p-4 rounded-3xl cursor-pointer text-center font-black transition-all';
            document.getElementById('label-takeaway').className = !isDine ? 'flex-1 border-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 text-orange-600 p-4 rounded-3xl cursor-pointer text-center font-black transition-all' : 'flex-1 border-4 border-transparent bg-gray-100 dark:bg-gray-700 text-gray-400 p-4 rounded-3xl cursor-pointer text-center font-black transition-all';
        }

        function searchMenu() {
            const val = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = c.dataset.name.includes(val) ? 'flex' : 'none');
        }

        function filterMenu(k) {
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = (k === 'semua' || c.dataset.category === k) ? 'flex' : 'none');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const active = btn.innerText.includes(k === 'semua' ? 'Semua' : k);
                btn.className = active ? 'filter-btn bg-orange-500 text-white px-8 py-3 rounded-2xl font-bold shadow-lg transition whitespace-nowrap' : 'filter-btn bg-white dark:bg-gray-800 text-gray-500 px-8 py-3 rounded-2xl font-bold border-2 border-transparent hover:border-orange-500 transition whitespace-nowrap';
            });
        }

        function validateAndSubmit() {
            if (!document.getElementById('selected_table_id').value) { alert("Pilih Meja dulu rek!"); openTableModal(); return; }
            if (cart.length === 0) { alert("Isi keranjang dulu!"); return; }
            document.getElementById('orderForm').submit();
        }
    </script>
</body>
</html>