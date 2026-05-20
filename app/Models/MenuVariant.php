<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuVariant extends Model
{
    protected $fillable = ['menu_id', 'variant_name', 'options', 'default_option'];

    // Biar otomatis jadi Array pas dikirim ke JavaScript
    protected $casts = [
        'options' => 'array',
    ];
}