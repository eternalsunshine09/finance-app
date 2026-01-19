<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            
            // 1. Cek Kolom bank_name
            if (!Schema::hasColumn('wallets', 'bank_name')) {
                $table->string('bank_name')->default('Cash')->after('user_id');
            }

            // 2. Cek Kolom account_name
            if (!Schema::hasColumn('wallets', 'account_name')) {
                // Pastikan urutannya benar, taruh setelah bank_name (kalau ada) atau user_id
                $after = Schema::hasColumn('wallets', 'bank_name') ? 'bank_name' : 'user_id';
                $table->string('account_name')->default('Main Wallet')->after($after);
            }

            // 3. Cek Kolom account_number
            if (!Schema::hasColumn('wallets', 'account_number')) {
                $after = Schema::hasColumn('wallets', 'account_name') ? 'account_name' : 'user_id';
                $table->string('account_number')->nullable()->after($after);
            }
            
        });
    }

    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Hapus kolom hanya jika ada
            if (Schema::hasColumn('wallets', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('wallets', 'account_name')) {
                $table->dropColumn('account_name');
            }
            if (Schema::hasColumn('wallets', 'account_number')) {
                $table->dropColumn('account_number');
            }
        });
    }
};