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
            
            // Kita ubah jadi (30, 8). 
            // Total 30 digit = Sisa 22 digit angka bulat (Cukup untuk Triliunan Rupiah)
            
            // 1. Perbaiki kolom AMOUNT
            if (Schema::hasColumn('transactions', 'amount')) {
                $table->decimal('amount', 30, 8)->change();
            } else {
                $table->decimal('amount', 30, 8)->after('user_id');
            }

            // 2. Perbaiki kolom PRICE_PER_UNIT
            if (Schema::hasColumn('transactions', 'price_per_unit')) {
                $table->decimal('price_per_unit', 30, 8)->change();
            } else {
                $after = Schema::hasColumn('transactions', 'amount') ? 'amount' : 'user_id';
                $table->decimal('price_per_unit', 30, 8)->default(0)->after($after);
            }
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