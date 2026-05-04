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
    Schema::table('menus', function (Blueprint $table) {
        // 'default' untuk minuman biasa, 'bottled' untuk air mineral/teh botol
        $table->string('type')->default('default')->after('category_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            //
        });
    }
};
