<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dapur - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#f8f9fa] text-gray-800 min-h-screen font-sans antialiased">
    
    <!-- Navbar / Header -->
    <header class="bg-white border-b border-gray-200 px-8 py-5 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <h1 class="text-3xl font-serif font-bold text-gray-900 italic tracking-tight">Ulam Sari</h1>
            <span class="text-xl font-bold text-gray-500 border-l-2 border-gray-300 pl-4">Dapur</span>
        </div>

        <!-- Tabs Filter -->
        <div class="flex bg-gray-100 rounded-full p-1.5">
            <a href="{{ route('dapur.index', ['jenis' => 'semua']) }}" 
               class="px-8 py-2 rounded-full text-base font-bold transition-all {{ $jenis == 'semua' ? 'bg-white shadow-md text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                Semua
            </a>
            <a href="{{ route('dapur.index', ['jenis' => 'dine-in']) }}" 
               class="px-8 py-2 rounded-full text-base font-bold transition-all {{ $jenis == 'dine-in' ? 'bg-white shadow-md text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                Dine In
            </a>
            <a href="{{ route('dapur.index', ['jenis' => 'take-away']) }}" 
               class="px-8 py-2 rounded-full text-base font-bold transition-all {{ $jenis == 'take-away' ? 'bg-white shadow-md text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                Take Away
            </a>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 text-gray-600 bg-gray-100 px-4 py-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="clock" class="font-mono font-bold text-lg"></span>
            </div>
            
            <form action="{{ route('pin.index') }}" method="GET">
                <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-2 bg-gray-100 rounded-lg hover:bg-red-50">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Grid Pesanan Utama (DIKASIH GAP & UKURAN LEBIH GEDE) -->
    <main class="p-8">
        <!-- Kolom dikurangi jadi 3 maksimal agar kartu lebih lebar -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($orders as $order)
            
            @php
                $isCooking = $order->order_status_id == 2;
                $cardBorder = $isCooking ? 'border-green-500 ring-4 ring-green-100 shadow-lg shadow-green-100/50' : 'border-gray-200 shadow-md';
                $headerBg = $isCooking ? 'bg-green-50' : 'bg-gray-50';
                $headerText = $isCooking ? 'text-green-700' : 'text-gray-800';
            @endphp

            <div class="bg-white rounded-2xl border-2 {{ $cardBorder }} flex flex-col h-[500px] overflow-hidden transition-all">
                
                <!-- Card Header -->
                <div class="{{ $headerBg }} border-b border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <!-- Ukuran Meja Diperbesar -->
                            <h2 class="font-black text-4xl {{ $headerText }}">Meja {{ $order->table_id }}</h2>
                            
                            <!-- Label Dine-In / Take Away Biar Jelas -->
                            <div class="mt-2">
                                @if($order->order_type == 'dine-in')
                                    <span class="bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-md border border-blue-200 tracking-wide">DINE IN</span>
                                @elseif($order->order_type == 'take-away')
                                    <span class="bg-orange-100 text-orange-800 text-sm font-bold px-3 py-1 rounded-md border border-orange-200 tracking-wide">TAKE AWAY</span>
                                @endif
                            </div>
                        </div>

                        <!-- Waktu -->
                        <div class="text-right">
                            <span class="text-lg font-mono font-bold text-gray-600 block">
                                {{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}
                            </span>
                            <span class="elapsed text-sm font-bold text-red-500" data-created-at="{{ $order->created_at->timestamp }}"></span>
                        </div>
                    </div>
                </div>

                <!-- Card Body (List Menu) -->
                <div class="p-6 flex-1 overflow-y-auto hide-scrollbar">
                    <ul class="space-y-5">
                    </ul>
                </div>

                <!-- Card Footer (Tombol) -->
                <div class="p-6 pt-0 mt-auto bg-white">
                    <form action="{{ route('dapur.update', $order->id) }}" method="POST">
                        @csrf
                        @if($order->order_status_id == 1)
                            <!-- Tombol Merah Diperbesar -->
                            <button type="submit" class="w-full bg-[#d32f2f] hover:bg-red-700 text-white font-black text-xl py-4 rounded-xl transition-transform active:scale-95 shadow-md">
                                MULAI MASAK
                            </button>
                        @elseif($order->order_status_id == 2)
                            <!-- Tombol Hijau Diperbesar -->
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-black text-xl py-4 rounded-xl transition-transform active:scale-95 shadow-md">
                                SELESAI
                            </button>
                        @endif
                    </form>
                </div>
            </div>

            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-32 text-gray-400">
                <svg class="w-24 h-24 mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-3xl font-black text-gray-400">Belum ada pesanan</h3>
                <p class="text-lg font-medium mt-2">Dapur masih aman terkendali.</p>
            </div>
            @endforelse
        </div>
    </main>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta'
            });
        }

        function updateElapsedTimes() {
            const elements = document.querySelectorAll('.elapsed');
            const now = Math.floor(Date.now() / 1000);
            elements.forEach(el => {
                const createdAt = parseInt(el.dataset.createdAt);
                const diff = now - createdAt;
                const minutes = Math.floor(diff / 60);
                
                // Ganti warna teks kalau udah kelamaan (contoh: lebih dari 15 menit)
                if (minutes >= 15) {
                    el.classList.add('text-red-600');
                    el.classList.remove('text-red-500');
                }
                
                el.innerText = `(+${minutes}m)`; 
            });
        }

        updateClock();
        updateElapsedTimes();
        setInterval(updateClock, 1000);
        setInterval(updateElapsedTimes, 60000); 
        
        setInterval(() => {
            window.location.reload();
        }, 15000);
    </script>
</body>
</html>