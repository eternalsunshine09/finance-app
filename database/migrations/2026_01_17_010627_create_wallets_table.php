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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Urutan di sini menentukan urutan di database
            // HAPUS ->after(...) di baris-baris ini:
            $table->string('currency', 3); 
            $table->string('bank_name')->default('Cash'); 
            $table->string('account_name')->default('Main Wallet');
            $table->string('account_number')->nullable();
            
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};