<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk nampilin halaman Kasir
Route::get('/kasir', [CashierController::class, 'index']);

// Route untuk nerima data form pesanan (INI YANG BIKIN ERROR KALAU GAADA)
Route::post('/kasir/pesan', [CashierController::class, 'store'])->name('kasir.store');