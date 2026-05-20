<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\MenuController;

Route::get('/', fn() => redirect()->route('pin.index'));

Route::get('/login-pin', [PinController::class, 'show'])->name('pin.index');
Route::post('/login-pin/verify', [PinController::class, 'verify'])->name('pin.verify');
Route::post('/login-pin/logout/{role}', [PinController::class, 'logout'])->name('pin.logout');

Route::prefix('pesan')->group(function () {
    Route::get('/{meja}', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::get('/success/{id}', [PelangganController::class, 'success'])->name('pelanggan.success');
});

Route::middleware('pin.auth:kasir')->prefix('kasir')->group(function () {
    Route::get('/', [CashierController::class, 'index'])->name('kasir.index');
    Route::post('/pesan', [CashierController::class, 'store'])->name('kasir.store');
    Route::post('/konfirmasi/{id}', [CashierController::class, 'konfirmasi'])->name('kasir.konfirmasi');
    Route::get('/export', [CashierController::class, 'export'])->name('kasir.export');
    Route::get('/nota/{id}', [CashierController::class, 'getNota'])->name('kasir.nota');
    Route::get('/api/pending-orders', [CashierController::class, 'apiPendingOrders'])->name('kasir.api.pending');
});

Route::middleware('pin.auth:dapur')->prefix('dapur')->group(function () {
    Route::get('/', [DapurController::class, 'index'])->name('dapur.index');
    Route::post('/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.updateStatus');
});

Route::middleware('pin.auth:admin')->prefix('admin')->group(function () {
    
    // UBAH BAGIAN INI: Bikin dia redirect ke controller menu buatan temenmu
    Route::get('/', function () {
        return redirect()->route('admin.menu.index');
    })->name('admin.index');
    
    // Rute di bawahnya biarin aja sama persis
    Route::get('/menu', [MenuController::class, 'index'])->name('admin.menu.index');
    Route::post('/menu/store', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::post('/menu/update/{id}', [MenuController::class, 'update'])->name('admin.menu.update');
    Route::get('/menu/toggle/{id}', [MenuController::class, 'toggleActive'])->name('admin.menu.toggleActive');
    Route::get('/menu/destroy/{id}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');
});