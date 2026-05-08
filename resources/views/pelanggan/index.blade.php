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
        /* Animasi mulus buat modal & cart */
        .slide-up { animation: slideUp 0.3s ease-out forwards; }
        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans antialiased">

    {{-- Notifikasi --}}
    @if(session('error'))
        <div id="alert-error" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-4 py-2 rounded-full font-bold shadow-xl text-sm w-11/12 max-w-sm text-center">
            {{ session('error') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-error').remove(), 3000);</script>
    @endif
    @if(session('success'))
        <div id="alert-success" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-4 py-2 rounded-full font-bold shadow-xl text-sm w-11/12 max-w-sm text-center">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('alert-success').remove(), 3000);</script>
    @endif

    {{-- CONTAINER UTAMA (UKURAN HP) --}}
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 min-h-screen shadow-2xl relative pb-24 overflow-x-hidden">
        
        {{-- HEADER --}}
        <div class="bg-white dark:bg-gray-800 sticky top-0 z-10 px-4 pt-6 pb-4 border-b dark:border-gray-700 flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Selamat Datang di</p>
                <h1 class="text-2xl font-black text-orange-600">Ulam Sari</h1>
            </div>
            <div class="bg-orange-100 text-orange-600 px-3 py-1 rounded-xl flex flex-col items-center justify-center border border-orange-200">
                <span class="text-[8px] font-bold uppercase">Meja</span>
                <span class="text-xl font-black leading-none">{{ $meja }}</span>
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

        {{-- KATEGORI (SCROLL MENYAMPING) --}}
        <div class="px-4 mt-6">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition">Semua</button>
                <button type="button" onclick="filterMenu('Ter-favorit')" class="filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition">Ter-favorit</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition">Makanan Berat</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition">Minuman</button>
            </div>
        </div>

        {{-- DAFTAR MENU (GRID 2 KOLOM) --}}
        <div class="px-4 mt-6">
            <div class="grid grid-cols-2 gap-4" id="menuGrid">
                @foreach($menus as $menu)
                <div class="menu-card bg-white dark:bg-gray-900 rounded-2xl border dark:border-gray-700 overflow-hidden shadow-sm flex flex-col h-full"
                    data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    <div class="relative pb-[100%]">
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 text-xs font-bold">FOTO</div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-1 justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100 leading-tight mb-1">{{ $menu->name }}</h3>
                            <p class="text-orange-500 font-bold text-xs mb-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                        <button type="button" onclick="openAddModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ addslashes($menu->category_name) }}')"
                            class="w-full bg-black dark:bg-gray-700 text-white text-xs font-bold py-2 rounded-xl mt-auto hover:bg-orange-500 transition">
                            Tambah
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        {{-- RUANG KOSONG BAWAH AGAR TIDAK TERTUTUP CART --}}
        <div class="h-10"></div>

    </div> {{-- END CONTAINER --}}

    {{-- FLOATING CART BUTTON --}}
    <div id="floating-cart" class="fixed bottom-4 left-1/2 -translate-x-1/2 w-11/12 max-w-[400px] z-20 hidden transition-all duration-300">
        <button onclick="openCartModal()" class="w-full bg-orange-500 text-white p-4 rounded-2xl flex justify-between items-center shadow-[0_8px_30px_rgb(0,0,0,0.12)]">
            <div class="flex items-center gap-3">
                <span id="cart-qty" class="bg-white text-orange-600 font-black w-7 h-7 rounded-full flex items-center justify-center text-xs">0</span>
                <span class="font-bold text-sm">Lihat Pesanan</span>
            </div>
            <span id="cart-total" class="font-black text-lg">Rp 0</span>
        </button>
    </div>

    {{-- ===== MODAL PILIH OPSI ITEM ===== --}}
    <div id="itemModal" class="fixed inset-0 bg-black/60 hidden items-end sm:items-center justify-center z-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl p-6 slide-up max-h-[85vh] overflow-y-auto scrollbar-hide pb-10">
            <div class="flex justify-between items-start mb-4 border-b dark:border-gray-700 pb-4">
                <div>
                    <h2 id="modalItemName" class="text-xl font-bold text-gray-800 dark:text-white leading-tight">Nama Item</h2>
                    <p id="modalItemPrice" class="text-orange-500 font-bold text-lg">Rp 0</p>
                </div>
                <button type="button" onclick="closeModal('itemModal')" class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <input type="hidden" id="modalItemId">
            
            <div class="space-y-4">
                <div id="chickenPartContainer" class="hidden">
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-1">Bagian Ayam</label>
                    <select id="chickenPart" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm bg-gray-50 dark:bg-gray-900 outline-none dark:text-white">
                        <option value="Bebas">Bebas</option><option value="Dada">Dada</option><option value="Paha">Paha</option><option value="Sayap">Sayap</option>
                    </select>
                </div>

                <div id="spicyLevelContainer" class="hidden">
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-1">Pedas</label>
                    <select id="spicyLevel" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm bg-gray-50 dark:bg-gray-900 outline-none dark:text-white">
                        <option value="Tidak Pedas">Tidak Pedas</option><option value="Sedang">Sedang</option><option value="Pedas">Pedas</option>
                    </select>
                </div>

                <div id="iceLevelContainer" class="hidden">
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-1">Es</label>
                    <select id="iceLevel" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm bg-gray-50 dark:bg-gray-900 outline-none dark:text-white">
                        <option value="Es Normal">Es Normal</option><option value="Less Ice">Less Ice</option><option value="Tanpa Es">Tanpa Es</option>
                    </select>
                </div>

                <div id="sugarLevelContainer" class="hidden">
                    <label class="block font-bold text-xs text-gray-400 uppercase mb-1">Gula</label>
                    <select id="sugarLevel" class="w-full border-2 border-gray-100 dark:border-gray-700 p-3 rounded-xl font-bold text-sm bg-gray-50 dark:bg-gray-900 outline-none dark:text-white">
                        <option value="Gula Normal">Gula Normal</option><option value="Less Sugar">Less Sugar</option><option value="Tanpa Gula">Tanpa Gula</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="font-bold text-gray-600 dark:text-gray-300 text-sm">Jumlah</span>
                    <div class="flex items-center gap-3 bg-gray-100 dark:bg-gray-700 p-1 rounded-xl">
                        <button type="button" onclick="changeQty(-1)" class="w-8 h-8 bg-white dark:bg-gray-800 rounded-lg font-black shadow-sm text-gray-600 dark:text-gray-300">-</button>
                        <input type="number" id="modalQty" value="1" min="1" class="w-8 text-center font-bold bg-transparent outline-none dark:text-white" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-8 h-8 bg-orange-500 text-white rounded-lg font-black shadow-sm">+</button>
                    </div>
                </div>

                <button type="button" onclick="saveToCart()" class="w-full bg-black text-white py-3 rounded-xl font-bold text-sm mt-4 shadow-lg active:scale-95 transition">
                    Masukkan ke Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL KERANJANG & CHECKOUT ===== --}}
    <div id="cartModal" class="fixed inset-0 bg-black/60 hidden items-end sm:items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 w-full max-w-md h-[90vh] sm:h-[80vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col slide-up">
            
            <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800 rounded-t-3xl sm:rounded-3xl">
                <div>
                    <h2 class="text-xl font-black text-gray-800 dark:text-white">Keranjang Anda</h2>
                    <p class="text-xs text-orange-500 font-bold">Meja {{ $meja }}</p>
                </div>
                <button type="button" onclick="closeModal('cartModal')" class="bg-gray-100 dark:bg-gray-700 p-2 rounded-full text-gray-500 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Tempat Daftar Item --}}
            <div id="cart-container" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900">
                </div>

            {{-- Form Submit --}}
            {{-- CATATAN: Pastikan route di bawah ini mengarah ke fungsi store yang ada di Kasir/Pelanggan controller --}}
            <form action="{{ route('kasir.store') }}" method="POST" id="checkoutForm" class="p-4 border-t dark:border-gray-700 bg-white dark:bg-gray-800">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data_input">
                <input type="hidden" name="table_id" value="{{ $meja }}"> {{-- <== INI KUNCI AUTO-TABLE --}}
                <input type="hidden" name="payment_type" value="later">
                <input type="hidden" name="payment_method" value="Belum Bayar">

                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm font-bold text-gray-500 uppercase">Total Pesanan</span>
                    <span id="checkout-total" class="text-2xl font-black text-orange-600">Rp 0</span>
                </div>
                
                <button type="button" onclick="submitCheckout()" class="w-full bg-orange-500 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-[0_8px_20px_rgba(249,115,22,0.3)] active:scale-95 transition">
                    Kirim Pesanan
                </button>
                <p class="text-center text-[9px] text-gray-400 font-bold mt-3">Pembayaran dilakukan di Kasir.</p>
            </form>
        </div>
    </div>

    {{-- KODE JAVASCRIPT --}}
    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // =====================
        // FILTER & SEARCH
        // =====================
        function searchMenu() {
            const val = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = c.dataset.name.includes(val) ? 'flex' : 'none');
        }

        function filterMenu(k) {
            document.querySelectorAll('.menu-card').forEach(c => c.style.display = (k === 'semua' || c.dataset.category === k) ? 'flex' : 'none');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                const active = btn.innerText.includes(k === 'semua' ? 'Semua' : k);
                btn.className = active
                    ? 'filter-btn bg-orange-500 text-white px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-sm transition'
                    : 'filter-btn bg-gray-50 dark:bg-gray-700 text-gray-500 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-gray-100 dark:border-gray-600 transition';
            });
        }

        // =====================
        // MODAL ADD ITEM
        // =====================
        function openAddModal(id, name, price, cat) {
            // Reset input
            document.getElementById('modalQty').value = 1;
            document.getElementById('chickenPart').value = 'Bebas';
            document.getElementById('spicyLevel').value = 'Tidak Pedas';
            document.getElementById('iceLevel').value = 'Es Normal';
            document.getElementById('sugarLevel').value = 'Gula Normal';

            // Set Data
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;

            // Opsi Tampilan
            const isAyam = name.toLowerCase().includes('ayam');
            const isMinuman = cat === 'Minuman';
            document.getElementById('chickenPartContainer').classList.toggle('hidden', !isAyam);
            document.getElementById('spicyLevelContainer').classList.toggle('hidden', isMinuman);
            document.getElementById('iceLevelContainer').classList.toggle('hidden', !isMinuman);
            document.getElementById('sugarLevelContainer').classList.toggle('hidden', !isMinuman);

            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.replace('flex', 'hidden');
        }

        function changeQty(v) {
            const q = document.getElementById('modalQty');
            if (parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v;
        }

        // =====================
        // CART LOGIC
        // =====================
        function saveToCart() {
            const id = document.getElementById('modalItemId').value;
            const name = document.getElementById('modalItemName').innerText;
            const price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice);
            const qty = parseInt(document.getElementById('modalQty').value);

            let notes = ['Dine In']; // Paksa Dine in karena pesan dari meja
            if (!document.getElementById('chickenPartContainer').classList.contains('hidden') && document.getElementById('chickenPart').value !== 'Bebas') {
                notes.push(document.getElementById('chickenPart').value);
            }
            if (!document.getElementById('spicyLevelContainer').classList.contains('hidden')) {
                notes.push(document.getElementById('spicyLevel').value);
            }
            if (!document.getElementById('iceLevelContainer').classList.contains('hidden')) {
                notes.push(document.getElementById('iceLevel').value);
            }
            if (!document.getElementById('sugarLevelContainer').classList.contains('hidden')) {
                notes.push(document.getElementById('sugarLevel').value);
            }

            const noteStr = notes.join(' • ');
            const itemData = { menu_id: id, name, price, qty, subtotal: price * qty, notes: noteStr };

            // Cek jika item yang sama persis (termasuk catatan) sudah ada
            const dup = cart.findIndex(i => i.menu_id === id && i.notes === noteStr);
            if (dup > -1) {
                cart[dup].qty += qty;
                cart[dup].subtotal = cart[dup].qty * price;
            } else {
                cart.unshift(itemData);
            }

            closeModal('itemModal');
            updateCartUI();
        }

        function updateCartUI() {
            let total = 0;
            let qty = 0;
            
            cart.forEach(item => {
                total += item.subtotal;
                qty += item.qty;
            });

            // Update Floating Cart
            const floatingCart = document.getElementById('floating-cart');
            if (cart.length > 0) {
                floatingCart.classList.remove('hidden');
                document.getElementById('cart-qty').innerText = qty;
                document.getElementById('cart-total').innerText = formatRupiah(total);
            } else {
                floatingCart.classList.add('hidden');
            }

            // Update Modal Cart (Checkout)
            const container = document.getElementById('cart-container');
            document.getElementById('checkout-total').innerText = formatRupiah(total);
            document.getElementById('cart_data_input').value = JSON.stringify(cart);

            container.innerHTML = '';
            if (cart.length === 0) {
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full pt-10 text-gray-400 italic font-bold text-sm"><p>Keranjang kosong.</p></div>`;
                return;
            }

            cart.forEach((item, i) => {
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl flex justify-between items-center shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <h4 class="font-bold text-sm text-gray-800 dark:text-white leading-tight">${item.name}</h4>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 mb-1 italic">${item.notes}</p>
                            <span class="text-orange-500 font-bold text-sm">${formatRupiah(item.price)}</span>
                        </div>
                        <div class="flex flex-col items-end gap-2 ml-2">
                            <span class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-lg text-xs font-black">x${item.qty}</span>
                            <button type="button" onclick="removeItem(${i})" class="text-xs font-bold text-red-500 hover:text-red-700">Hapus</button>
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
            // Auto close jika keranjang kosong
            if(cart.length === 0) { closeModal('cartModal'); }
        }

        function submitCheckout() {
            if (cart.length === 0) { alert("Pilih menu dulu ya!"); return; }
            // Submit data
            document.getElementById('checkoutForm').submit();
        }
    </script>
</body>
</html>