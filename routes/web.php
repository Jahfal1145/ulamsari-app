<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\PelangganController;

// 1. Home / Landing
Route::get('/', function () {
    return redirect()->route('kasir.index');
});

// 2. Route Kasir
Route::prefix('kasir')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('kasir.index');
    Route::post('/pesan', [CashierController::class, 'store'])->name('kasir.store');
});

// 3. Route Pelanggan (Scan QR)
Route::prefix('pesan')->group(function () {
    Route::get('/{meja}', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
});

// 4. Route Dapur
Route::prefix('dapur')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('dapur.index');
    Route::post('/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update');
});
=======
// Route untuk Dapur
Route::get('/dapur', [DapurController::class, 'index'])->name('dapur.index');
Route::post('/dapur/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update');

>>>>>>> d74b414c12822598d9b58a32d3fd0aa8e61ccb71
