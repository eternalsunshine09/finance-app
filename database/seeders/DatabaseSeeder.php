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
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@finance.com',
            'password' => Hash::make('password'), // Passwordnya: password
            'role' => 'admin',
        ]);

        // 2. Buat Akun USER BIASA (Namanya Budi)
        $user = User::create([
            'name' => 'Budi Investor',
            'email' => 'budi@finance.com',
            'password' => Hash::make('password'), // Passwordnya: password
            'role' => 'user',
        ]);

        // 3. Beri Budi Modal Awal (Dompet)
        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'IDR',
            'balance' => 100000000, // Modal Rp 100 Juta
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'balance' => 0, // Saldo Dollar masih 0
        ]);

        // 4. Masukkan Data Master Aset (Biar bisa dibeli)
        $assets = [
            ['symbol' => 'ANTM', 'name' => 'Aneka Tambang Tbk', 'type' => 'Stock'],
            ['symbol' => 'BBCA', 'name' => 'Bank Central Asia Tbk', 'type' => 'Stock'],
            ['symbol' => 'BTC',  'name' => 'Bitcoin', 'type' => 'Crypto'],
            ['symbol' => 'USD',  'name' => 'US Dollar', 'type' => 'Currency'],
        ];

        foreach ($assets as $asset) {
            Asset::create($asset);
        }

        // 5. Masukkan Kurs Dollar Hari Ini
        ExchangeRate::create([
            'from_currency' => 'USD',
            'to_currency' => 'IDR',
            'rate' => 15500, // 1 USD = Rp 15.500
            'date' => now(),
        ]);
    }
}