<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ulam Sari Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-black relative">

    <form action="{{ route('kasir.store') }}" method="POST" class="flex h-screen overflow-hidden">
        @csrf 
        <input type="hidden" name="cart_data" id="cart_data_input">

        <div class="w-3/5 p-6 overflow-y-auto flex flex-col relative z-0">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-black text-orange-600 tracking-tighter">PILIH MENU</h2>
                
                <div class="relative w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu favoritmu..." 
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-2xl focus:border-orange-500 outline-none transition shadow-sm font-medium">
                </div>
            </div>
            
            <div class="flex gap-3 mb-8 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn whitespace-nowrap bg-orange-500 text-white px-8 py-2.5 rounded-full font-bold shadow-md transition">Menu</button>
                <button type="button" onclick="filterMenu('Ter-favorit')" class="filter-btn whitespace-nowrap bg-white text-black px-8 py-2.5 rounded-full font-bold border-2 border-gray-100 hover:border-orange-500 transition">Ter-favorit</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn whitespace-nowrap bg-white text-black px-8 py-2.5 rounded-full font-bold border-2 border-gray-100 hover:border-orange-500 transition">Makanan Berat</button>
                <button type="button" onclick="filterMenu('Makanan Ringan')" class="filter-btn whitespace-nowrap bg-white text-black px-8 py-2.5 rounded-full font-bold border-2 border-gray-100 hover:border-orange-500 transition">Makanan Ringan</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn whitespace-nowrap bg-white text-black px-8 py-2.5 rounded-full font-bold border-2 border-gray-100 hover:border-orange-500 transition">Minuman</button>
            </div>

            <div class="grid grid-cols-2 gap-8 pb-20" id="menuGrid">
                @foreach($menus as $menu)
                <div onclick="openModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }}, '{{ $menu->category_name }}')" 
                     class="menu-card bg-white rounded-[2rem] shadow-sm border-2 border-transparent overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-orange-500 flex flex-col h-full cursor-pointer group animate-slide-up" 
                     data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    
                    <div class="h-44 bg-gray-50 flex items-center justify-center relative overflow-hidden">
                        <span class="text-gray-300 font-bold uppercase tracking-widest text-xs">Foto {{ $menu->name }}</span>
                        <div class="absolute inset-0 bg-orange-600 bg-opacity-0 group-hover:bg-opacity-5 transition-all"></div>
                    </div>
                    
                    <div class="p-6 flex flex-col flex-1 relative bg-white">
                        <h3 class="font-black text-xl leading-tight mb-2 text-black uppercase tracking-tight">{{ $menu->name }}</h3>
                        <p class="text-orange-500 font-black text-2xl">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        
                        <div class="absolute bottom-6 right-6 bg-gray-100 p-4 rounded-2xl text-black group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="w-2/5 bg-white p-6 shadow-2xl flex flex-col z-10 border-l border-gray-200">
            
            <div class="mb-8 border-b-2 border-gray-50 pb-6">
                <label class="block font-black text-gray-400 text-xs uppercase tracking-[0.2em] mb-3">Customer Location</label>
                <input type="hidden" name="table_id" id="selected_table_id" required>
                <button type="button" onclick="openTableModal()" 
                        class="w-full bg-white text-black border-2 border-gray-200 p-5 rounded-2xl font-black text-xl hover:border-orange-500 transition flex justify-between items-center shadow-sm active:scale-95 group">
                    <span id="table_label">PILIH NOMOR MEJA</span>
                    <svg class="w-6 h-6 text-orange-500 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>
                </button>
            </div>

            <h2 class="text-2xl font-black mb-4 text-black flex items-center gap-2">
                DETAIL PESANAN 
                <span id="cart-count" class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">0</span>
            </h2>

            <div id="cart-container" class="flex-1 overflow-y-auto pr-2 space-y-4">
                <div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300">
                    <svg class="w-20 h-20 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg>
                    <p class="font-bold uppercase tracking-widest text-sm">Belum ada menu</p>
                </div>
            </div>

            <div class="border-t-4 border-gray-50 pt-6 mt-4">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-gray-400 font-bold uppercase tracking-widest text-sm">Total Tagihan</span>
                    <span id="total-price" class="text-black font-black text-4xl tracking-tighter">Rp 0</span>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-5 rounded-[1.5rem] font-black text-2xl hover:bg-black shadow-xl transition-all duration-300 transform active:scale-95 uppercase tracking-widest">
                    Kirim Pesanan
                </button>
            </div>
        </div>
    </form>

    <div id="tableModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z- backdrop-blur-md">
        <div class="bg-white w-[500px] rounded-[2.5rem] shadow-2xl p-10 transform transition-all animate-slide-up">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-black text-black tracking-tighter">DENAH MEJA</h2>
                <p class="text-orange-500 font-bold uppercase text-xs tracking-[0.3em]">Ulam Sari Digital Order</p>
            </div>
            
            <div class="grid grid-cols-4 gap-4 mb-10">
                @for ($i = 1; $i <= 12; $i++)
                    <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}"
                            class="meja-option aspect-square flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 bg-white hover:border-orange-500 transition-all active:scale-90 group shadow-sm">
                        <span class="text-[10px] font-black text-gray-300 group-hover:text-orange-400">MEJA</span>
                        <span class="text-3xl font-black text-black group-hover:text-orange-600">{{ $i }}</span>
                    </button>
                @endfor
            </div>
            
            <button type="button" onclick="closeTableModal()" class="w-full bg-black text-white py-4 rounded-2xl font-bold uppercase tracking-widest hover:bg-orange-600 transition-colors">Batal</button>
        </div>
    </div>

    <div id="itemModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 backdrop-blur-md">
        <div class="bg-white w-[450px] rounded-[2.5rem] shadow-2xl p-8 transform transition-all animate-slide-up" id="modalContent">
            <div class="flex justify-between items-start mb-8 border-b border-gray-100 pb-6">
                <div>
                    <h2 id="modalItemName" class="text-3xl font-black text-black tracking-tighter uppercase">Nama Item</h2>
                    <p id="modalItemPrice" class="text-orange-500 font-black text-2xl">Rp 0</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-black p-3 bg-gray-50 rounded-2xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>
                </button>
            </div>

            <input type="hidden" id="modalItemId">
            
            <div class="space-y-6 mb-10">
                <div>
                    <label class="block font-black text-gray-400 text-xs uppercase tracking-widest mb-3">Service Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 border-2 border-gray-100 p-4 rounded-2xl cursor-pointer text-center font-black transition-all has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 has-[:checked]:text-orange-600">
                            <input type="radio" name="orderType" value="Dine In" class="hidden" checked> DINE IN
                        </label>
                        <label class="flex-1 border-2 border-gray-100 p-4 rounded-2xl cursor-pointer text-center font-black transition-all has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 has-[:checked]:text-orange-600">
                            <input type="radio" name="orderType" value="Takeaway" class="hidden"> TAKEAWAY
                        </label>
                    </div>
                </div>

                <div id="chickenPartContainer" class="hidden">
                    <label class="block font-black text-gray-400 text-xs uppercase tracking-widest mb-2">Bagian Ayam</label>
                    <select id="chickenPart" class="w-full border-2 border-gray-100 p-4 rounded-2xl focus:border-orange-500 outline-none font-bold text-lg appearance-none bg-gray-50">
                        <option value="Bebas">BEBAS PILIH</option><option value="Dada">DADA</option><option value="Paha Atas">PAHA ATAS</option><option value="Sayap">SAYAP</option>
                    </select>
                </div>

                <div id="spicyLevelContainer" class="hidden">
                    <label class="block font-black text-gray-400 text-xs uppercase tracking-widest mb-2">Level Pedas</label>
                    <select id="spicyLevel" class="w-full border-2 border-gray-100 p-4 rounded-2xl focus:border-orange-500 outline-none font-bold text-lg appearance-none bg-gray-50">
                        <option value="Sedang">SEDANG</option><option value="Tidak Pedas">TIDAK PEDAS</option><option value="Pedas">PEDAS</option><option value="Ekstra Pedas">EKSTRA PEDAS</option>
                    </select>
                </div>

                <div>
                    <label class="block font-black text-gray-400 text-xs uppercase tracking-widest mb-3">Quantity</label>
                    <div class="flex items-center gap-6 bg-gray-50 p-3 rounded-[1.5rem] w-full justify-between border-2 border-gray-100">
                        <button type="button" onclick="changeQty(-1)" class="w-12 h-12 bg-white rounded-xl shadow-sm font-black text-2xl hover:bg-black hover:text-white transition-all">-</button>
                        <input type="number" id="modalQty" value="1" min="1" class="w-20 text-center font-black text-3xl bg-transparent outline-none" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-12 h-12 bg-white rounded-xl shadow-sm font-black text-2xl hover:bg-orange-500 hover:text-white transition-all">+</button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addToCart()" class="w-full bg-black text-white py-5 rounded-[1.5rem] font-black text-xl shadow-xl hover:bg-orange-600 transition-all uppercase tracking-widest">Add to Order</button>
        </div>
    </div>

    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // SEARCH
        function searchMenu() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                card.style.display = card.getAttribute('data-name').includes(input) ? 'flex' : 'none';
            });
        }

        // FILTER
        function filterMenu(kat) {
            document.querySelectorAll('.menu-card').forEach(card => {
                card.style.display = (kat === 'semua' || card.getAttribute('data-category') === kat) ? 'flex' : 'none';
            });
            document.querySelectorAll('.filter-btn').forEach(btn => {
                let active = btn.innerText === (kat === 'semua' ? 'Menu' : kat);
                btn.className = active ? 'filter-btn whitespace-nowrap bg-orange-500 text-white px-8 py-2.5 rounded-full font-bold shadow-md transition'
                                       : 'filter-btn whitespace-nowrap bg-white text-black px-8 py-2.5 rounded-full font-bold border-2 border-gray-100 hover:border-orange-500 transition';
            });
        }

        // MEJA LOGIC
        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }
        function selectTable(num) {
            document.getElementById('table_label').innerText = "MEJA NOMOR " + num;
            document.getElementById('selected_table_id').value = num;
            document.querySelectorAll('.meja-option').forEach(b => {
                b.classList.remove('border-orange-500', 'bg-orange-50');
                b.classList.add('border-gray-100', 'bg-white');
            });
            let active = document.getElementById('btn-meja-'+num);
            active.classList.replace('border-gray-100', 'border-orange-500');
            active.classList.replace('bg-white', 'bg-orange-50');
            setTimeout(closeTableModal, 200);
        }

        // MODAL ITEM LOGIC
        function openModal(id, name, price, cat) {
            document.getElementById('modalItemId').value = id;
            document.getElementById('modalItemName').innerText = name;
            document.getElementById('modalItemPrice').innerText = formatRupiah(price);
            document.getElementById('modalItemPrice').dataset.rawPrice = price;
            document.getElementById('modalQty').value = 1;
            document.getElementById('chickenPartContainer').classList.toggle('hidden', !name.toLowerCase().includes('ayam'));
            document.getElementById('spicyLevelContainer').classList.toggle('hidden', cat.toLowerCase() === 'minuman');
            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }
        function closeModal() { document.getElementById('itemModal').classList.replace('flex', 'hidden'); }
        function changeQty(v) { 
            let q = document.getElementById('modalQty');
            if(parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v;
        }

        function addToCart() {
            let id = document.getElementById('modalItemId').value, name = document.getElementById('modalItemName').innerText,
                price = parseInt(document.getElementById('modalItemPrice').dataset.rawPrice), qty = parseInt(document.getElementById('modalQty').value),
                type = document.querySelector('input[name="orderType"]:checked').value,
                spicy = !document.getElementById('spicyLevelContainer').classList.contains('hidden') ? document.getElementById('spicyLevel').value : null,
                part = !document.getElementById('chickenPartContainer').classList.contains('hidden') ? document.getElementById('chickenPart').value : null;

            let notes = [type]; if(part && part !== 'Bebas') notes.push(part); if(spicy) notes.push(spicy);
            cart.push({ menu_id: id, name, price, qty, subtotal: price * qty, notes: notes.join(' • ') });
            closeModal(); updateCartUI();
        }

        function removeItem(i) { cart.splice(i, 1); updateCartUI(); }

        function updateCartUI() {
            let container = document.getElementById('cart-container'), totalEl = document.getElementById('total-price'),
                countEl = document.getElementById('cart-count');
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            container.innerHTML = ''; let total = 0;
            countEl.innerText = cart.length;

            if (cart.length === 0) { container.appendChild(document.getElementById('empty-cart-msg')); totalEl.innerText = 'Rp 0'; return; }
            
            cart.forEach((item, i) => {
                total += item.subtotal;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white border-2 border-gray-100 rounded-[1.5rem] p-5 flex justify-between items-center group hover:border-orange-500 transition-all duration-300 shadow-sm animate-slide-up">
                        <div class="flex-1">
                            <h4 class="font-black text-black text-lg uppercase leading-tight">${item.name}</h4>
                            <p class="text-[10px] font-black text-orange-500 tracking-widest mt-1 mb-2 uppercase opacity-80">${item.notes}</p>
                            <div class="flex items-center gap-3">
                                <span class="bg-black text-white px-3 py-1 rounded-lg text-xs font-black">x${item.qty}</span>
                                <span class="text-black font-black text-lg">${formatRupiah(item.subtotal)}</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeItem(${i})" class="text-gray-200 hover:text-red-600 transition-colors p-2">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                        </button>
                    </div>
                `);
            });
            totalEl.innerText = formatRupiah(total);
        }
    </script>
</body>
</html>