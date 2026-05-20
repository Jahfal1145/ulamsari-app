<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 text-center">
        
        @if($order->order_status_id == 4)
            {{-- TAMPILAN JIKA BELUM BAYAR --}}
            <div class="w-20 h-20 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h1 class="text-xl font-black text-gray-800 mb-2 uppercase">Menunggu Pembayaran</h1>
            <p class="text-gray-500 text-xs mb-6 leading-relaxed">Pesanan Anda sudah tercatat, namun <b>belum diteruskan ke dapur</b>. Silakan selesaikan pembayaran terlebih dahulu.</p>
        @else
            {{-- TAMPILAN JIKA SUDAH BAYAR --}}
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-xl font-black text-gray-800 mb-2 uppercase">Pembayaran Berhasil!</h1>
            <p class="text-gray-500 text-xs mb-6">Pesanan Anda <b>sedang dimasak</b> oleh tim dapur Ulam Sari.</p>
        @endif
        
        <div class="bg-gray-50 rounded-2xl p-4 mb-6 border-2 border-dashed border-gray-200">
            <p class="text-[9px] font-bold text-gray-400 uppercase">Nomer Pesanan</p>
            <p class="text-lg font-black text-orange-600 tracking-tighter">{{ $order->order_number }}</p>
            <div class="h-px bg-gray-200 my-2"></div>
            <p class="text-[9px] font-bold text-gray-400 uppercase">Total yang harus dibayar</p>
            <p class="text-2xl font-black text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        @if($order->order_status_id == 4 && $order->payment_method == 'Tunai')
            <div class="bg-orange-600 text-white p-4 rounded-2xl text-xs font-bold leading-relaxed mb-6 shadow-lg">
                Sebutkan Nomer Pesanan di atas ke Kasir untuk membayar secara Tunai.
            </div>
        @endif

        <div class="flex flex-col gap-2">
    <button onclick="window.location.reload()" class="w-full bg-gray-200 text-gray-700 py-3 rounded-xl font-bold text-xs uppercase transition active:scale-95">
        Cek Status Bayar
    </button>
    
    {{-- Ubah href menjadi route Laravel --}}
    <a href="{{ route('pelanggan.index', $order->table_id) }}" class="text-gray-400 text-[10px] font-bold uppercase mt-2 hover:text-orange-500 transition">
        Pesan Menu Lain
    </a>
</div>
    </div>
</body>
</html>