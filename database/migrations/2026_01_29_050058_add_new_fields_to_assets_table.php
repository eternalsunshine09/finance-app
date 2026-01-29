<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Hapus kolom lama jika ada
            $table->dropColumn('logo');
            
            // Tambah kolom baru
            $table->string('logo_url')->nullable()->after('symbol');
            $table->date('price_updated_at')->nullable()->after('current_price');
            
            // Untuk Mutual Fund
            $table->string('mutual_fund_type')->nullable()->after('type');
            $table->decimal('management_fee', 5, 2)->nullable();
            $table->decimal('minimum_purchase', 15, 2)->nullable();
            $table->string('risk_level')->nullable();
            $table->string('investment_manager')->nullable();
            $table->string('manager_website')->nullable();
            $table->date('launch_date')->nullable();
            $table->string('category')->nullable();
            
            // Untuk US Stock
            $table->string('sector')->nullable();
            $table->string('exchange')->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->string('country')->nullable();
            $table->string('company_website')->nullable();
            $table->string('ceo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->string('logo')->nullable();
            
            // Hapus kolom baru
            $table->dropColumn([
                'logo_url',
                'price_updated_at',
                'mutual_fund_type',
                'management_fee',
                'minimum_purchase',
                'risk_level',
                'investment_manager',
                'manager_website',
                'launch_date',
                'category',
                'sector',
                'exchange',
                'market_cap',
                'country',
                'company_website',
                'ceo',
            ]);
        });
    }
};