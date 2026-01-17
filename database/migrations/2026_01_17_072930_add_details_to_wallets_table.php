<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Nama Bank/Sekuritas (Misal: BCA, Bibit, Ajaib, Binance)
            $table->string('bank_name')->default('Cash')->after('user_id'); 

            // Nama Akun (Misal: Tabungan Utama, RDN Saham, Dompet Kripto)
            $table->string('account_name')->default('Main Wallet')->after('bank_name');

            // Nomor Rekening (Opsional, buat gaya-gayaan)
            $table->string('account_number')->nullable()->after('account_name');
        });
    }

    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_name', 'account_number']);
        });
    }
};