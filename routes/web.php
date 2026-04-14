<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashierController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk halaman Kasir
Route::get('/kasir', [CashierController::class, 'index']);