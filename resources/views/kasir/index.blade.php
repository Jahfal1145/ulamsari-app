<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 relative">

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

        <div class="w-3/5 p-6 overflow-y-auto flex flex-col relative z-0 border-r">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-orange-600 tracking-tight uppercase">Pilih Menu</h2>
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="searchMenu()" placeholder="Cari menu..." 
                           class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl focus:border-orange-500 outline-none font-semibold shadow-sm">
                </div>
            </div>
            
            <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide">
                <button type="button" onclick="filterMenu('semua')" class="filter-btn bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md transition">Menu</button>
                <button type="button" onclick="filterMenu('Ter-favorit')" class="filter-btn bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-orange-50 hover:text-orange-500 transition">Ter-favorit</button>
                <button type="button" onclick="filterMenu('Makanan Berat')" class="filter-btn bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-orange-50 hover:text-orange-500 transition">Makanan Berat</button>
                <button type="button" onclick="filterMenu('Minuman')" class="filter-btn bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-orange-50 hover:text-orange-500 transition">Minuman</button>
            </div>

            <div class="grid grid-cols-2 gap-6 pb-20" id="menuGrid">
                @foreach($menus as $menu)
                <div id="menu-item-{{ $menu->id }}" 
                     onclick="openAddModal({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }}, '{{ $menu->category_name }}')" 
                     class="menu-card bg-white rounded-2xl shadow-sm border overflow-hidden transition hover:shadow-xl hover:border-orange-400 flex flex-col h-full cursor-pointer group" 
                     data-category="{{ $menu->category_name }}" data-name="{{ strtolower($menu->name) }}">
                    <div class="h-40 bg-gray-200 flex items-center justify-center text-gray-400 text-sm italic font-medium uppercase text-center p-2">Foto {{ $menu->name }}</div>
                    <div class="p-5 flex flex-col flex-1 relative bg-white border-t">
                        <h3 class="font-bold text-xl leading-tight mb-2 text-gray-800">{{ $menu->name }}</h3>
                        <p class="text-orange-500 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <div class="absolute bottom-4 right-4 bg-orange-100 p-3 rounded-full text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="w-2/5 bg-white p-6 shadow-2xl flex flex-col border-l">
            <div class="mb-6 border-b-2 border-gray-100 pb-6 text-center">
                <input type="hidden" name="table_id" id="selected_table_id">
                <button type="button" onclick="openTableModal()" 
                        class="w-full bg-white text-black border-2 border-gray-100 p-4 rounded-2xl font-bold text-xl hover:border-orange-500 transition flex justify-center items-center shadow-sm relative group">
                    <span id="table_label" class="uppercase">Nomor Meja</span>
                    <svg class="w-6 h-6 text-orange-500 absolute right-4 group-hover:translate-y-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                </button>
            </div>

            <h2 class="text-2xl font-bold mb-4 text-gray-800 uppercase tracking-tight">Detail Pesanan</h2>
            <div id="cart-container" class="flex-1 overflow-y-auto pr-2 space-y-3">
                <div id="empty-cart-msg" class="flex flex-col items-center justify-center h-full text-gray-300 italic font-bold">
                    <p>BELUM ADA MENU DIPILIH</p>
                </div>
            </div>

            <div class="border-t-2 border-gray-100 pt-4 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-500 text-lg uppercase font-bold">Total</span>
                    <span id="total-price" class="text-orange-600 text-3xl font-bold">Rp 0</span>
                </div>
                <button type="button" onclick="validateAndSubmit()" class="w-full bg-orange-500 text-white py-5 rounded-2xl font-bold text-2xl hover:bg-black shadow-xl transition transform active:scale-95 uppercase tracking-wider">
                    Kirim Pesanan
                </button>
            </div>
        </div>
    </form>

    <div id="tableModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z- backdrop-blur-sm">
        <div class="bg-white w-[480px] rounded-3xl shadow-2xl p-8 transform transition-all">
            <h2 class="text-2xl font-bold text-center mb-8 uppercase">Denah Meja</h2>
            <div class="grid grid-cols-4 gap-4 mb-8">
                @for ($i = 1; $i <= 12; $i++)
                    <button type="button" onclick="selectTable('{{ $i }}')" id="btn-meja-{{ $i }}"
                            class="meja-option aspect-square flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 bg-white hover:border-orange-500 transition-all active:scale-90 shadow-sm font-bold group">
                        <span class="text-[10px] text-gray-300 group-hover:text-orange-400 uppercase">MEJA</span>
                        <span class="text-2xl text-black group-hover:text-orange-600">{{ $i }}</span>
                    </button>
                @endfor
            </div>
            <button type="button" onclick="closeTableModal()" class="w-full bg-black text-white py-3 rounded-xl font-bold uppercase transition hover:bg-orange-600">Tutup</button>
        </div>
    </div>

    <div id="itemModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z- backdrop-blur-sm">
        <div class="bg-white w-[420px] rounded-[2.5rem] shadow-2xl p-8" id="modalContent">
            <div class="flex justify-between items-start mb-6 border-b pb-4">
                <div>
                    <h2 id="modalItemName" class="text-2xl font-bold text-gray-800 uppercase">Nama Item</h2>
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
                    <label class="block font-bold text-gray-600 text-sm mb-2 uppercase italic">Bagian Ayam:</label>
                    <select id="chickenPart" class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-orange-500 font-bold bg-gray-50 outline-none">
                        <option value="Bebas">Bebas</option><option value="Dada">Dada</option><option value="Paha">Paha</option><option value="Sayap">Sayap</option>
                    </select>
                </div>

                <div id="spicyLevelContainer" class="hidden">
                    <label class="block font-bold text-gray-600 text-sm mb-2 uppercase italic">Pedas:</label>
                    <select id="spicyLevel" class="w-full border-2 border-gray-100 p-3 rounded-xl focus:border-orange-500 font-bold bg-gray-50 outline-none">
                        <option value="Tidak Pedas">Tidak Pedas</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Pedas">Pedas</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-600 text-sm mb-2 uppercase italic">Jumlah:</label>
                    <div class="flex items-center gap-4 bg-gray-50 p-2 rounded-2xl w-fit border">
                        <button type="button" onclick="changeQty(-1)" class="w-10 h-10 bg-white rounded-xl shadow-sm font-black text-xl hover:bg-orange-500 hover:text-white transition">-</button>
                        <input type="number" id="modalQty" value="1" min="1" class="w-12 text-center font-bold text-xl bg-transparent outline-none" readonly>
                        <button type="button" onclick="changeQty(1)" class="w-10 h-10 bg-white rounded-xl shadow-sm font-black text-xl hover:bg-orange-500 hover:text-white transition">+</button>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <label class="block font-bold text-gray-600 text-sm mb-3 uppercase italic">Tipe Pesanan:</label>
                    <div class="flex gap-4">
                        <label id="label-dinein" class="flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-orange-500 bg-orange-50 text-orange-600 transition-all">
                            <input type="radio" name="orderType" value="Dine In" class="hidden" checked onchange="toggleServiceUI()"> Dine In
                        </label>
                        <label id="label-takeaway" class="flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-gray-100 text-gray-500 bg-white transition-all">
                            <input type="radio" name="orderType" value="Takeaway" class="hidden" onchange="toggleServiceUI()"> Takeaway
                        </label>
                    </div>
                </div>

                <button type="button" onclick="saveToCart()" id="btn-submit-modal" class="w-full bg-black text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-orange-600 transition uppercase tracking-widest">Tambahkan</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        const formatRupiah = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

        // VALIDASI SEBELUM KIRIM
        function validateAndSubmit() {
            const tableId = document.getElementById('selected_table_id').value;
            if (!tableId) {
                alert("Waduh, Nomor Meja belum dipilih rek! Klik tombol di atas rincian pesanan ya.");
                openTableModal();
                return;
            }
            if (cart.length === 0) {
                alert("Keranjang masih kosong, pilih menu dulu!");
                return;
            }
            document.getElementById('orderForm').submit();
        }

        // SEARCH & FILTER
        function searchMenu() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => card.style.display = card.getAttribute('data-name').includes(input) ? 'flex' : 'none');
        }

        function filterMenu(kat) {
            document.querySelectorAll('.menu-card').forEach(card => card.style.display = (kat === 'semua' || card.getAttribute('data-category') === kat) ? 'flex' : 'none');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                let active = btn.innerText === (kat === 'semua' ? 'Menu' : kat);
                btn.className = active ? 'filter-btn bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md' : 'filter-btn bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-orange-50 transition';
            });
        }

        // MEJA
        function openTableModal() { document.getElementById('tableModal').classList.replace('hidden', 'flex'); }
        function closeTableModal() { document.getElementById('tableModal').classList.replace('flex', 'hidden'); }
        function selectTable(num) {
            document.getElementById('table_label').innerText = "MEJA " + num;
            document.getElementById('selected_table_id').value = num;
            document.querySelectorAll('.meja-option').forEach(b => { b.classList.remove('border-orange-500', 'bg-orange-50'); b.classList.add('border-gray-100', 'bg-white'); });
            let active = document.getElementById('btn-meja-'+num);
            active.classList.replace('border-gray-100', 'border-orange-500'); active.classList.replace('bg-white', 'bg-orange-50');
            setTimeout(closeTableModal, 200);
        }

        // MODAL ADD & EDIT
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

            let cat = 'Makanan';
            document.querySelectorAll('.menu-card').forEach(card => {
                if(card.querySelector('h3').innerText === item.name) cat = card.dataset.category;
            });
            checkVisibility(item.name, cat);

            let notes = item.notes.split(' • ');
            if(notes === 'Takeaway') document.querySelector('input[name="orderType"][value="Takeaway"]').checked = true;
            if(notes.length > 1) {
                notes.forEach(n => {
                    if(n === "Dada" || n === "Paha" || n === "Sayap") document.getElementById('chickenPart').value = n;
                    if(n.includes("Pedas")) document.getElementById('spicyLevel').value = n;
                });
            }
            toggleServiceUI();
            document.getElementById('btn-submit-modal').innerText = "Update Item";
            document.getElementById('itemModal').classList.replace('hidden', 'flex');
        }

        function checkVisibility(name, cat) {
            const n = name.toLowerCase();
            document.getElementById('chickenPartContainer').classList.toggle('hidden', !n.includes('ayam'));
            document.getElementById('spicyLevelContainer').classList.toggle('hidden', cat.toLowerCase() === 'minuman');
        }

        function resetModalFields() {
            document.getElementById('modalQty').value = 1;
            document.getElementById('spicyLevel').value = "Tidak Pedas";
            document.getElementById('chickenPart').value = "Bebas";
            document.querySelector('input[name="orderType"][value="Dine In"]').checked = true;
            toggleServiceUI();
        }

        function toggleServiceUI() {
            const isDineIn = document.querySelector('input[name="orderType"]:checked').value === 'Dine In';
            document.getElementById('label-dinein').className = isDineIn ? "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-orange-500 bg-orange-50 text-orange-600 transition-all" : "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-gray-100 text-gray-500 bg-white transition-all";
            document.getElementById('label-takeaway').className = !isDineIn ? "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-orange-500 bg-orange-50 text-orange-600 transition-all" : "flex-1 border-2 p-3 rounded-xl cursor-pointer text-center font-bold border-gray-100 text-gray-500 bg-white transition-all";
        }

        function closeModal() { document.getElementById('itemModal').classList.replace('flex', 'hidden'); }
        function changeQty(v) { let q = document.getElementById('modalQty'); if(parseInt(q.value) + v >= 1) q.value = parseInt(q.value) + v; }

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
            if(part && part !== 'Bebas') noteParts.push(part);
            if(spicy) noteParts.push(spicy);
            let notes = noteParts.join(' • ');

            if (editIndex > -1) {
                cart[editIndex] = { menu_id: id, name, price, qty, subtotal: price * qty, notes };
            } else {
                let dup = cart.findIndex(i => i.menu_id === id && i.notes === notes);
                if (dup > -1) {
                    cart[dup].qty += qty;
                    cart[dup].subtotal = cart[dup].qty * price;
                } else {
                    cart.push({ menu_id: id, name, price, qty, subtotal: price * qty, notes });
                }
            }
            closeModal();
            updateCartUI();
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

        function updateCartUI() {
            let container = document.getElementById('cart-container'), totalEl = document.getElementById('total-price');
            document.getElementById('cart_data_input').value = JSON.stringify(cart);
            container.innerHTML = ''; let total = 0;
            if (cart.length === 0) { container.appendChild(document.getElementById('empty-cart-msg')); totalEl.innerText = 'Rp 0'; return; }
            
            cart.forEach((item, i) => {
                total += item.subtotal;
                container.insertAdjacentHTML('beforeend', `
                    <div class="bg-white border-2 border-gray-100 rounded-2xl p-4 flex justify-between items-center shadow-sm hover:border-orange-500 transition">
                        <div class="flex-1 cursor-pointer" onclick="openEditModal(${i})">
                            <h4 class="font-bold text-black text-lg uppercase">${item.name}</h4>
                            <p class="text-[10px] font-bold text-orange-500 tracking-widest mt-1 mb-2 uppercase italic">${item.notes}</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-black text-white px-2 py-0.5 rounded-lg text-xs font-bold uppercase">x${item.qty}</span>
                                <span class="text-black font-bold text-lg">${formatRupiah(item.subtotal)}</span>
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
    </script>
</body>
</html>