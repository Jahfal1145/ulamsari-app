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
    ];

        public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}