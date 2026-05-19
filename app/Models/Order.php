<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    
    protected $fillable = [
        'table_id',
        'order_number',
        'total_price',
        'order_status_id',
        'payment_method', // 👇 INI DIA KUNCI JAWABANNYA 👇
        'customer_name',  // <--- Tambahkan ini
        'phone_number',   // <--- Tambahkan ini
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}