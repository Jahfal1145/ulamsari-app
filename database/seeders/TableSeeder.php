<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run()
    {
        // Matikan sementara pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel
        DB::table('tables')->truncate();

        // Nyalakan kembali foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert data meja
        for ($i = 1; $i <= 12; $i++) {
            DB::table('tables')->insert([
                'id' => $i,
                'table_number' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}