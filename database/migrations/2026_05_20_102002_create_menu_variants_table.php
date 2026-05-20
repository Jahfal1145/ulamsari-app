<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id();
            // Sambungkan ke tabel menus, kalau menu dihapus, variannya ikut musnah
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('variant_name');
            $table->json('options'); // Kita simpan opsi sebagai JSON Array biar gampang dipanggil JS
            $table->string('default_option')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
