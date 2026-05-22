<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Auth\PinController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuVariantController;

Route::get('/', function () {
    return redirect()->route('pin.index');
});

Route::get('/login-pin', [PinController::class, 'show'])
    ->name('pin.index');

    
Route::get('/login-pin', [PinController::class, 'show'])->name('pin.index');
Route::post('/login-pin/verify', [PinController::class, 'verify'])->name('pin.verify');
Route::post('/login-pin/logout/{role}', [PinController::class, 'logout'])->name('pin.logout');

Route::prefix('pesan')->group(function () {
    Route::get('/{meja}', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::post('/store', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::get('/success/{id}', [PelangganController::class, 'success'])->name('pelanggan.success');
});

Route::get('/menu/{id}/variants', [MenuController::class, 'getVariants'])->name('menu.variants.public');

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
        // ── Varian Menu (CRUD via AJAX) ──────────────────────────
    Route::get('/menu/{id}/variants', [MenuController::class, 'getVariants'])->name('admin.menu.variants.get');
    Route::post('/menu/{id}/variants', [MenuController::class, 'storeVariant'])->name('admin.menu.variants.store');
    Route::post('/menu/variants/{id}/update', [MenuVariantController::class, 'update']);
    Route::delete('/menu/variants/{id}/delete', [MenuVariantController::class, 'destroy']);
 
});

Route::get('/menu/{id}/variants', [MenuVariantController::class, 'index']);
    Route::post('/menu/{id}/variants', [MenuVariantController::class, 'store']);
    Route::post('/menu/variants/{id}/update', [MenuVariantController::class, 'update']);
    Route::get('/menu/variants/{id}/delete', [MenuVariantController::class, 'destroy']);
// =========================================================
// JALUR VVIP VARIAN MENU (Anti 404)
// =========================================================

// =========================================================
// OBAT ANTI ERROR "Route [login] not defined"
// =========================================================
Route::get('/login-darurat', function() {
    // Kalau sesi habis, otomatis dilempar balik ke halaman utama
    return redirect('/'); 
})->name('login');
 
    // ── ★ Kategori CRUD ──────────────────────────────────────────
    Route::get('/categories',                        [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories',                       [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::post('/categories/{id}/update',           [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::get('/categories/{id}/delete',            [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
 
// ── Route PUBLIK varian untuk halaman pelanggan ──────────────────
Route::get('/menu/{id}/variants', [MenuController::class, 'getVariants'])->name('menu.variants.public');

// API Kategori (Bisa kamu taruh di dalam middleware admin)
Route::get('/admin/categories', [App\Http\Controllers\CategoryController::class, 'apiIndex']);
Route::post('/admin/categories', [App\Http\Controllers\CategoryController::class, 'apiStore']);
Route::post('/admin/categories/{id}/update', [App\Http\Controllers\CategoryController::class, 'apiUpdate']);
Route::get('/admin/categories/{id}/delete', [App\Http\Controllers\CategoryController::class, 'apiDestroy']);

// Rute publik untuk ambil varian menu di halaman pelanggan
Route::get('/menu/{id}/variants', [App\Http\Controllers\MenuVariantController::class, 'index']);

Route::middleware('pin.auth:admin')->prefix('admin')->group(function () {

    Route::get('/menu/{id}/variants', [App\Http\Controllers\MenuVariantController::class, 'index']);

    Route::post('/menu/{id}/variants', [App\Http\Controllers\MenuVariantController::class, 'store']);

    Route::post('/menu/variants/{id}/update', [App\Http\Controllers\MenuVariantController::class, 'update']);

    Route::delete('/menu/variants/{id}/delete', [App\Http\Controllers\MenuVariantController::class, 'destroy']);

});