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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('asset_symbol'); // Contoh: BBCA
            $table->decimal('balance', 20, 8)->change();
            $table->decimal('quantity', 20, 10); // Jumlah aset (pakai desimal banyak biar support Crypto 0.00001 BTC)
            $table->decimal('average_buy_price', 15, 2); // Harga rata-rata beli
            $table->timestamps();
            
            // Mencegah duplikat: Satu user hanya boleh punya satu baris per simbol aset
            $table->unique(['user_id', 'asset_symbol']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};