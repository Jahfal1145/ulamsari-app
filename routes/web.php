<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DapurController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk nampilin halaman Kasir
Route::get('/kasir', [CashierController::class, 'index']);

// Route untuk nerima data form pesanan
Route::post('/kasir/pesan', [CashierController::class, 'store'])->name('kasir.store');

// Route untuk Dapur
Route::get('/dapur', [DapurController::class, 'index'])->name('dapur.index');
Route::post('/dapur/update/{id}', [DapurController::class, 'updateStatus'])->name('dapur.update');