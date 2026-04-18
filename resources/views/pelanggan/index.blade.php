<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Ulam Sari - Meja {{ $meja }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b px-6 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-orange-600 italic tracking-tighter uppercase leading-none">ULAM SARI</h1>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Digital Order</p>
        </div>
        <div class="bg-black text-white px-4 py-2 rounded-2xl font-black text-xs italic">
            MEJA {{ $meja }}
        </div>
    </div>

    <div class="p-6">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-orange-200 relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-black uppercase leading-[0.9]">AYAM BAKAR<br>ULAM SARI</h2>
                <p class="text-orange-100 text-[10px] font-bold mt-3 uppercase tracking-widest italic">Resep Rahasia Keluarga</p>
            </div>
            <svg class="absolute -right-6 -bottom-6 w-32 h-32 text-white/10 transform rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
        </div>
    </div>

    <div class="flex gap-3 overflow-x-auto px-6 pb-6 scrollbar-hide">
        <button class="bg-orange-500 text-white px-6 py-2.5 rounded-2xl font-black uppercase text-[10px] shadow-lg shadow-orange-100 transition">Semua</button>
        <button class="bg-white border border-gray-100 text-gray-400 px-6 py-2.5 rounded-2xl font-black uppercase text-[10px] transition">Makanan</button>
        <button class="bg-white border border-gray-100 text-gray-400 px-6 py-2.5 rounded-2xl font-black uppercase text-[10px] transition">Minuman</button>
    </div>

    <div class="px-6 pb-40">
        <div class="grid grid-cols-1 gap-4">
            @foreach($menus as $menu)
            <div class="bg-white rounded-[2rem] p-4 flex gap-4 shadow-sm border border-gray-50 active:scale-95 transition" 
                 onclick="addToCart({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }})">
                <div class="w-24 h-24 bg-gray-100 rounded-3xl flex-shrink-0 flex items-center justify-center text-[8px] text-gray-300 font-black uppercase text-center p-2 border-2 border-dashed border-gray-200">
                    FOTO {{ $menu->name }}
                </div>
                <div class="flex flex-col justify-between flex-1 py-1">
                    <div>
                        <h4 class="font-black text-gray-900 uppercase text-lg leading-tight">{{ $menu->name }}</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase italic mt-0.5">{{ $menu->category_name }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-orange-600 font-black text-xl italic">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        <div class="bg-black text-white p-2.5 rounded-xl shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('pelanggan.store') }}" method="POST" id="orderForm" class="hidden">
        @csrf
        <input type="hidden" name="table_id" value="{{ $meja }}">
        <input type="hidden" name="cart_data" id="cart_input">
    </form>

    <div id="checkout-bar" class="fixed bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-white via-white to-transparent z-50 translate-y-full transition-transform duration-300">
        <button onclick="submitOrder()" class="w-full bg-black text-white flex justify-between items-center p-5 rounded-[2.5rem] shadow-2xl active:scale-95 transition">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-black h-5 w-5 rounded-full flex items-center justify-center border-2 border-black">0</span>
                </div>
                <div class="flex flex-col items-start leading-none">
                    <span class="text-[9px] font-black uppercase text-gray-400 tracking-widest mb-1">Total Pesanan</span>
                    <span id="cart-total" class="text-xl font-black italic">Rp 0</span>
                </div>
            </div>
            <div class="bg-orange-500 text-white px-5 py-2.5 rounded-2xl font-black uppercase text-[10px] tracking-widest">
                PESAN SEKARANG
            </div>
        </button>
    </div>

    <script>
        let cart = [];

        function addToCart(id, name, price) {
            let index = cart.findIndex(i => i.menu_id === id);
            if (index > -1) {
                cart[index].qty++;
                cart[index].subtotal = cart[index].qty * price;
            } else {
                cart.push({ menu_id: id, name: name, price: price, qty: 1, subtotal: price, notes: 'Dine In' });
            }
            updateUI();
        }

        function updateUI() {
            const bar = document.getElementById('checkout-bar');
            const count = document.getElementById('cart-count');
            const total = document.getElementById('cart-total');
            
            let totalQty = cart.reduce((acc, i) => acc + i.qty, 0);
            let totalPrice = cart.reduce((acc, i) => acc + i.subtotal, 0);

            if (totalQty > 0) {
                bar.classList.remove('translate-y-full');
            } else {
                bar.classList.add('translate-y-full');
            }

            count.innerText = totalQty;
            total.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalPrice);
        }

        function submitOrder() {
            if (cart.length === 0) return;
            if (confirm('Kirim pesanan ke dapur sekarang?')) {
                document.getElementById('cart_input').value = JSON.stringify(cart);
                document.getElementById('orderForm').submit();
            }
        }
    </script>
</body>
</html>