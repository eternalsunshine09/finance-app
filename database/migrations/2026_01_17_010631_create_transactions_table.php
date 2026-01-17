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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // Hubungkan ke dompet mana uangnya diambil/disimpan
            
            // Tipe Transaksi
            $table->enum('type', ['TOPUP', 'WITHDRAW', 'BUY', 'SELL']);
            
            // Detail Aset (Nullable / Boleh Kosong jika cuma Topup uang)
            $table->string('asset_symbol')->nullable(); 
            
            // Nominal
            $table->decimal('amount_cash', 15, 2); // Uang yang keluar/masuk
            $table->decimal('amount_asset', 20, 10)->nullable(); // Jumlah aset yang didapat/dijual
            $table->decimal('price_per_unit', 15, 2)->nullable(); // Harga aset saat transaksi terjadi
            
            $table->date('date'); // Tanggal transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};