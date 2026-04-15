<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'subtotal',
        'notes',
    ];

        public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}