<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Dapur - Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 min-h-screen p-4 md:p-8 font-sans antialiased">
    
    <header class="flex justify-between items-end mb-8 pb-4 border-b-2 border-slate-800">
        <div>
            <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-yellow-500 tracking-tight">
                ULAM SARI <span class="text-slate-100">KITCHEN</span>
            </h1>
            <p class="text-slate-400 mt-1 text-sm font-medium">Sistem Antrean Dapur Real-time</p>
        </div>
        <div class="text-right flex flex-col items-end">
            <p id="clock" class="text-3xl font-mono font-bold text-slate-100"></p>
            <div class="flex items-center gap-2 mt-2">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-emerald-500 text-xs font-bold tracking-widest uppercase">Live System</span>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($orders as $order)
        @php
            // Mapping Status Angka ke Teks
            $statusTeks = [1 => 'PENDING', 2 => 'COOKING', 3 => 'READY'];
            $teks = $statusTeks[$order->order_status_id] ?? 'UNKNOWN';
        @endphp

        <div class="flex flex-col bg-slate-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 {{ $order->order_status_id == 2 ? 'ring-2 ring-yellow-500 shadow-yellow-900/20 scale-[1.02]' : 'border border-slate-700' }}">
            
            <div class="p-4 flex justify-between items-center {{ $order->order_status_id == 2 ? 'bg-yellow-500/10' : ($order->order_status_id == 3 ? 'bg-emerald-500/10' : 'bg-slate-800') }}">
                <div>
                    <p class="text-xs text-slate-400 font-semibold mb-1 uppercase tracking-wider">Nomor Meja</p>
                    <h2 class="text-3xl font-black text-white leading-none">{{ $order->table_id }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-mono mb-1">{{ $order->created_at->format('H:i') }}</p>
                    <span class="px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                        {{ $order->order_status_id == 1 ? 'bg-orange-500/20 text-orange-400' : 
                          ($order->order_status_id == 2 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-emerald-500/20 text-emerald-400') }}">
                        {{ $teks }}
                    </span>
                </div>
            </div>

            <div class="p-5 flex-1 overflow-y-auto hide-scrollbar bg-slate-800/50">
                <ul class="space-y-4">
                    @foreach($order->detail_pesanan as $item)
                    <li class="flex items-start gap-4 pb-4 border-b border-dashed border-slate-700 last:border-0 last:pb-0">
                        <div class="flex-shrink-0 bg-slate-700/50 border border-slate-600 rounded-lg px-3 py-2 text-center min-w-[3rem]">
                            <span class="block text-xl font-black text-orange-400">{{ $item->qty }}</span>
                        </div>
                        <div class="pt-1">
                            <h3 class="text-lg font-bold text-slate-200 leading-tight">{{ $item->name }}</h3>
                            @if(isset($item->notes) && $item->notes != '')
                                <p class="text-sm text-yellow-400 italic mt-1.5 flex items-start gap-1">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $item->notes }}
                                </p>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="p-4 bg-slate-900/50 mt-auto">
                <form action="{{ route('dapur.update', $order->id) }}" method="POST">
                    @csrf
                    @if($order->order_status_id == 1)
                        <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-3.5 rounded-xl transition-all active:scale-[0.98] shadow-lg shadow-orange-900/20 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>
                            MULAI MASAK
                        </button>
                    @elseif($order->order_status_id == 2)
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-bold py-3.5 rounded-xl transition-all active:scale-[0.98] shadow-lg shadow-emerald-900/20 flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            SELESAI MASAK
                        </button>
                    @else
                        <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-300 font-bold py-3.5 rounded-xl transition-all active:scale-[0.98] flex justify-center items-center gap-2">
                            SUDAH DIANTAR
                        </button>
                    @endif
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-32">
            <div class="bg-slate-800/50 p-8 rounded-full mb-6">
                <svg class="w-20 h-20 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-3xl font-black text-slate-500 mb-2">ANTREAN BERSIH!</h3>
            <p class="text-slate-500 font-medium">Koki bisa istirahat sejenak, belum ada pesanan masuk.</p>
        </div>
        @endforelse
    </div>

    <script>
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID', { hour12: false });
        }, 1000);
    </script>
</body>
</html>