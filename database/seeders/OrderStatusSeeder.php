<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder; // <--- Tambahkan baris ini
use Illuminate\Support\Facades\DB; // <--- Tambahkan ini juga agar DB tidak error

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_statuses')->insert([
            ['id' => 1, 'name' => 'Menunggu', 'color_code' => '#F59E0B'],
            ['id' => 2, 'name' => 'Dimasak', 'color_code' => '#3B82F6'],
            ['id' => 3, 'name' => 'Selesai', 'color_code' => '#10B981'],
        ]);
    }
}