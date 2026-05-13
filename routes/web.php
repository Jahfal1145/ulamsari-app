<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\MenuController;


// 1. Home / Landing
Route::get('/', function () {
    return redirect()->route('pin.index'); // Diarahkan ke login PIN dulu agar aman
});

// 2. Route Auth PIN
Route::get('/login-pin', [PinController::class, 'index'])->name('pin.index');
Route::post('/login-pin', [PinController::class, 'verify'])->name('pin.verify');

// 3. Route Kasir
Route::prefix('kasir')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('kasir.index');
    Route::post('/pesan', [CashierController::class, 'store'])->name('kasir.store');
    Route::post('/kasir/konfirmasi/{id}', [App\Http\Controllers\KasirController::class, 'konfirmasi'])->name('kasir.konfirmasi');
});

Route::get('/kasir/export', [App\Http\Controllers\CashierController::class, 'export'])->name('kasir.export');

// 3. Route Pelanggan (Scan QR)     
// 4. Route Pelanggan (Scan QR)

Route::prefix('pesan')->group(function () {
    Route::get('/{meja}', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
});

// 5. Route Dapur
Route::prefix('dapur')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('dapur.index');
    Route::post('/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update');
});

// 6. Route Admin / Kelola Menu
Route::prefix('admin/menu')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('admin.menu.index');
    Route::post('/store', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::post('/toggle/{id}', [MenuController::class, 'toggleActive'])->name('admin.menu.toggle');
    Route::delete('/destroy/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');
    Route::post('/update/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
});

// Penutup kurung kurawal yang error tadi sudah dihapus karena tidak ada pasangannya

// Pastikan baris ini ada dan namanya 'dapur.update-status'
Route::post('/dapur/update-status/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update-status');

Route::post('/pesan/store', [PelangganController::class, 'store'])->name('pelanggan.store');
Route::get('/pesan/success/{id}', [PelangganController::class, 'success'])->name('pelanggan.success');

