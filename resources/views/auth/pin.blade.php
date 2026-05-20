<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayam Bakar Ulam Sari - Login PIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen relative">

    @isset($remaining)
        <div class="absolute inset-0 bg-gray-900 bg-opacity-95 z-50 flex flex-col items-center justify-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mb-4 text-red-500 animate-pulse">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
            <h2 class="text-2xl font-bold mb-2">Akses Diblokir Sementara</h2>
            <p class="text-gray-300">Tunggu <span id="timer" class="font-bold text-red-400">{{ $remaining }}</span> detik untuk mencoba lagi.</p>
        </div>
        <script>
            let timeLeft = {{ $remaining }};
            const timerEl = document.getElementById('timer');
            const countdown = setInterval(() => {
                if (timeLeft > 0) { timerEl.innerText = --timeLeft; }
                else { clearInterval(countdown); window.location.reload(); }
            }, 1000);
        </script>
    @endisset

    <div class="bg-white p-8 rounded-3xl shadow-sm w-full max-w-sm flex flex-col items-center">

        {{-- Header --}}
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold italic text-gray-800">ayam bakar</h1>
            <h1 class="text-4xl font-serif text-gray-900 -mt-2">Ulam Sari</h1>
        </div>

        {{-- Error / Info --}}
        @if(session('error'))
            <div class="w-full text-center text-red-600 text-sm font-semibold mb-4 bg-red-50 py-2 rounded-lg border border-red-100">
                {{ session('error') }}
            </div>
        @else
            <p class="text-gray-400 text-sm mb-4">Masukkan PIN untuk masuk</p>
        @endif

        {{-- Dot Indicator (diperlebar) --}}
        <div id="pin-display" class="flex space-x-5 mb-10">
            @for($i = 0; $i < 6; $i++)
                <div class="w-5 h-5 rounded-full bg-gray-200 transition-colors duration-200"></div>
            @endfor
        </div>

        {{-- Numpad --}}
        <div class="grid grid-cols-3 gap-5 w-full">
            @foreach([1,2,3,4,5,6,7,8,9] as $num)
                <button type="button" onclick="addNumber('{{ $num }}')"
                    class="h-16 bg-gray-100 text-2xl font-semibold rounded-xl hover:bg-gray-200 active:scale-95 transition-all">
                    {{ $num }}
                </button>
            @endforeach
            <div class="h-16"></div>
            <button type="button" onclick="addNumber('0')"
                class="h-16 bg-gray-100 text-2xl font-semibold rounded-xl hover:bg-gray-200 active:scale-95 transition-all">
                0
            </button>
            <button type="button" onclick="deleteNumber()"
                class="h-16 bg-red-50 rounded-xl flex items-center justify-center hover:bg-red-100 active:scale-95 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75 14.25 12m0 0 2.25 2.25M14.25 12l2.25-2.25M14.25 12 12 14.25m-12 0 4.5 6h13.5a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 17.25 4.5H4.5L0 10.5v3Z" />
                </svg>
            </button>
        </div>

        {{-- Hidden Form --}}
        <form id="pin-form" action="{{ route('pin.verify') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="pin" id="pin-input">
        </form>
    </div>

    <script>
        let currentPin = "";
        const dots = document.querySelectorAll('#pin-display div');

        let isSubmitting = false; // Tambahkan kunci pelindung ini

        function addNumber(num) {
            if (document.getElementById('timer') || isSubmitting) return; // Stop kalau lagi diblokir atau loading
            
            if (currentPin.length < 6) {
                currentPin += num;
                updateDots();
            }
            
            if (currentPin.length === 6) {
                isSubmitting = true; // Kunci sistemnya biar nggak dobel klik
                document.getElementById('pin-input').value = currentPin;
                
                // Kasih delay dikit biar buletan terakhir sempet hitam sebelum pindah halaman
                setTimeout(() => { 
                    document.getElementById('pin-form').submit(); 
                }, 150);
            }
        }

        function deleteNumber() {
            currentPin = currentPin.slice(0, -1);
            updateDots();
        }

        function updateDots() {
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-gray-700', i < currentPin.length);
                dot.classList.toggle('bg-gray-200', i >= currentPin.length);
            });
        }
    </script>
</body>
</html>