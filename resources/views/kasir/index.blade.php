<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Ayam Bakar Ulam Sari</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <div class="w-3/5 p-6 overflow-y-auto flex flex-col">
            <h2 class="text-3xl font-extrabold text-orange-600 mb-6">Menu Ulam Sari</h2>
            
            <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
                <button class="bg-orange-500 text-white px-6 py-2 rounded-full font-semibold shadow-md">Semua</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-gray-50">Makanan</button>
                <button class="bg-white text-gray-600 px-6 py-2 rounded-full font-semibold border hover:bg-gray-50">Minuman</button>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden cursor-pointer hover:shadow-md transition">
                    <div class="h-32 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Foto Ayam Bakar</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg">Ayam Bakar Madu</h3>
                        <p class="text-orange-500 font-semibold mt-2">Rp 25.000</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden cursor-pointer hover:shadow-md transition">
                    <div class="h-32 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Foto Es Teh</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg">Es Teh Manis</h3>
                        <p class="text-orange-500 font-semibold mt-2">Rp 5.000</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/5 bg-white p-6 shadow-2xl flex flex-col z-10 border-l border-gray-200">
            
            <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-gray-100">
                <h2 class="text-2xl font-bold">Pesanan</h2>
                <button class="bg-blue-50 text-blue-600 border border-blue-200 px-4 py-2 rounded-lg font-bold hover:bg-blue-600 hover:text-white transition shadow-sm">
                    Meja: Pilih ▾
                </button>
            </div>

            <div class="flex-1 overflow-y-auto pr-2">
                <div class="flex justify-between items-center mb-4 p-3 bg-gray-50 rounded-lg border">
                    <div>
                        <h4 class="font-bold text-gray-700">Ayam Bakar Madu</h4>
                        <p class="text-sm text-gray-500">Pedes level 3</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-semibold">x1</span>
                        <span class="font-bold text-orange-600">25.000</span>
                    </div>
                </div>
            </div>

            <div class="border-t-2 border-gray-100 pt-6 mt-4">
                <div class="flex justify-between items-center font-bold text-2xl mb-6">
                    <span class="text-gray-600">Total:</span>
                    <span class="text-orange-600">Rp 25.000</span>
                </div>
                <button class="w-full bg-orange-500 text-white py-4 rounded-xl font-bold text-xl hover:bg-orange-600 shadow-lg transition">
                    Proses Pesanan
                </button>
            </div>

        </div>
    </div>

</body>
</html>