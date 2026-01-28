<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Menambahkan kolom change_percent setelah current_price
            // Tipe decimal agar bisa menyimpan angka desimal (misal: 1.25)
            // nullable() agar data lama tidak error
            $table->decimal('change_percent', 8, 2)->default(0)->nullable()->after('current_price');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('change_percent');
        });
    }
};