<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Cek dulu, kalau kolom 'image' belum ada, baru bikin!
        if (!Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('image')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};