<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'is_active',
        'image',
        'description'
    ];

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_menu',
            'menu_id',
            'category_id'
        );
    }

    // TAMBAHKAN FUNGSI INI UNTUK MEMBUKA PINTU DATA VARIAN
    public function variants()
    {
        // Sesuaikan 'Variant::class' jika nama modelmu berbeda (misal: MenuVariant::class)
        return $this->hasMany(\App\Models\MenuVariant::class); 
    } 
}