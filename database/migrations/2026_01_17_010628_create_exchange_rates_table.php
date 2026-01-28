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
    Schema::create('exchange_rates', function (Blueprint $table) {
        $table->id();
        $table->string('from_currency', 3); // USD
        $table->string('to_currency', 3)->default('IDR'); // IDR
        $table->decimal('rate', 15, 2);
        $table->timestamps();
        
        $table->unique(['from_currency', 'to_currency']); // Prevent duplicates
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};