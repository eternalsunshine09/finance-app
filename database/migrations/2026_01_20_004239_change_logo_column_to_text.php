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
        Schema::table('assets', function (Blueprint $table) {
            // Ubah jadi TEXT agar muat link panjang
            $table->text('logo')->nullable()->change(); 
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('logo')->nullable()->change();
        });
    }
};