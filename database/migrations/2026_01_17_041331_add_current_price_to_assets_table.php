<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Kita pakai decimal biar presisi untuk uang (15 digit, 2 desimal)
            // Taruh setelah kolom 'type'
            $table->decimal('current_price', 15, 2)->default(0)->after('type');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('current_price');
        });
    }
};