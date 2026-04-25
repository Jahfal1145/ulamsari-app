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

// Route untuk Dapur
Route::get('/dapur', [DapurController::class, 'index'])->name('dapur.index');
Route::post('/dapur/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update');

// --- ROUTE ADMIN / KELOLA MENU ---
Route::prefix('admin/menu')->group(function () {
    Route::get('/', [App\Http\Controllers\MenuController::class, 'index'])->name('admin.menu.index');
    Route::post('/store', [App\Http\Controllers\MenuController::class, 'store'])->name('admin.menu.store');
    Route::post('/toggle/{id}', [App\Http\Controllers\MenuController::class, 'toggleActive'])->name('admin.menu.toggle');
    Route::delete('/destroy/{id}', [App\Http\Controllers\MenuController::class, 'destroy'])->name('admin.menu.destroy');
    
    // 👇 TAMBAHKAN BARIS INI UNTUK UPDATE MENU 👇
    Route::post('/update/{id}', [App\Http\Controllers\MenuController::class, 'update'])->name('admin.menu.update');
});
