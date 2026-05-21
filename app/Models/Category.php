<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    protected $fillable = ['name'];

    public function menus()
    {
        return $this->belongsToMany(
            Menu::class,
            'category_menu',
            'category_id',
            'menu_id'
        );
    }
}