<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- INI YANG HILANG TADI
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    // Pastikan semua kolom yang boleh diisi ada di sini
    protected $fillable = ['name', 'price', 'category_id', 'is_active', 'image', 'description'];
}