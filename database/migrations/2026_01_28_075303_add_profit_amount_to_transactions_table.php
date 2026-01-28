<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus "after('total')" karena kolom 'total' mungkin tidak ada
            // Biarkan MySQL menaruhnya di urutan terakhir secara default
            if (!Schema::hasColumn('transactions', 'profit_amount')) {
                $table->decimal('profit_amount', 15, 2)->default(0)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'profit_amount')) {
                $table->dropColumn('profit_amount');
            }
        });
    }
};