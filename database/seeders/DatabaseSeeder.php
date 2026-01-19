<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Wallet;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        // Pakai updateOrCreate biar kalau di-seed 2x tidak error duplikat
        User::updateOrCreate(
            ['email' => 'admin@finance.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'), // Pass: password
                'role' => 'admin',
            ]
        );

        // 2. Buat Akun USER BIASA (Budi)
        $user = User::updateOrCreate(
            ['email' => 'budi@finance.com'],
            [
                'name' => 'Budi Investor',
                'password' => Hash::make('password'), // Pass: password
                'role' => 'user',
            ]
        );

        // 3. Beri Budi Modal Awal (Dompet)
        // UPDATE: Menambahkan bank_name, account_name, account_number
        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'IDR',
            'balance' => 100000000, // Modal Rp 100 Juta
            'bank_name' => 'Bank Central Asia',
            'account_name' => 'Tabungan Utama',
            'account_number' => '1234567890',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 0, // Saldo Dollar 0
            'bank_name' => 'PayPal',
            'account_name' => 'USD Assets',
            'account_number' => 'user_budi_usd',
        ]);

        // 4. Masukkan Data Master Aset
        $assets = [
            ['symbol' => 'ANTM', 'name' => 'Aneka Tambang Tbk', 'type' => 'Stock'],
            ['symbol' => 'BBCA', 'name' => 'Bank Central Asia Tbk', 'type' => 'Stock'],
            ['symbol' => 'BTC',  'name' => 'Bitcoin', 'type' => 'Crypto'],
            ['symbol' => 'USD',  'name' => 'US Dollar', 'type' => 'Currency'],
        ];

        foreach ($assets as $asset) {
            Asset::updateOrCreate(['symbol' => $asset['symbol']], $asset);
        }

        // 5. Masukkan Kurs Dollar Hari Ini
        ExchangeRate::create([
            'from_currency' => 'USD',
            'to_currency' => 'IDR',
            'rate' => 15500,
            'date' => now(),
        ]);
    }
}