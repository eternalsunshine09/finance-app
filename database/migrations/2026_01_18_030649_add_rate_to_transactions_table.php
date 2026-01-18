<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menyimpan nilai kurs saat transaksi terjadi
            $table->decimal('exchange_rate', 15, 2)->nullable()->after('amount_cash'); 
            // Menyimpan mata uang tujuan (misal convert IDR ke USD)
            $table->string('target_currency', 3)->nullable()->after('exchange_rate');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'target_currency']);
        });
    }
};