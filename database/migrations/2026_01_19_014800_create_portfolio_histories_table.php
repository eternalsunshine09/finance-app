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
        // Pastikan nama tabel sama persis: 'portfolio_histories'
        Schema::create('portfolio_histories', function (Blueprint $table) {
            $table->id();
            
            // Kolom Wajib untuk Controller
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke user
            $table->date('date'); // Untuk sumbu X grafik (Tanggal)
            $table->decimal('total_value', 15, 2); // Untuk sumbu Y grafik (Nilai Uang)
            
            $table->timestamps();

            // Mencegah error duplikat data
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_histories');
    }
};