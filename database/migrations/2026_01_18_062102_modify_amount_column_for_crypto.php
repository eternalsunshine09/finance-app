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
    Schema::table('transactions', function (Blueprint $table) {
        // DECIMAL(20, 8) artinya: Total 20 digit, dengan 8 digit di belakang koma
        // Contoh max: 999999999999.12345678
        $table->decimal('amount', 20, 8)->change(); 
        $table->decimal('price_per_unit', 20, 2)->change(); // Harga biasanya cukup 2 desimal
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};