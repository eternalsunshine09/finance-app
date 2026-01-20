<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PortfolioHistory;
use App\Models\Wallet;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlySnapshot extends Command
{
    protected $signature = 'snapshot:monthly {--date=}';
    protected $description = 'Generate monthly portfolio snapshot for all users';

    public function handle()
    {
        $date = $this->option('date') 
            ? Carbon::parse($this->option('date'))
            : Carbon::now()->endOfMonth();

        $users = User::all();
        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        foreach ($users as $user) {
            // Hitung total kekayaan
            $totalCash = Wallet::where('user_id', $user->id)->sum('balance');
            
            $totalInvestment = 0;
            $portfolios = DB::table('portfolios')
                ->join('assets', 'portfolios.asset_symbol', '=', 'assets.symbol')
                ->where('portfolios.user_id', $user->id)
                ->where('portfolios.quantity', '>', 0)
                ->get();

            foreach ($portfolios as $porto) {
                $nilaiSekarang = $porto->quantity * $porto->current_price;
                $totalInvestment += ($porto->type == 'Crypto') 
                    ? $nilaiSekarang * $usdRate 
                    : $nilaiSekarang;
            }

            $totalWealth = $totalCash + $totalInvestment;

            // Simpan snapshot bulanan
            PortfolioHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                ],
                [
                    'total_value' => $totalWealth,
                ]
            );

            $this->info("Snapshot created for {$user->name}: Rp " . number_format($totalWealth, 0, ',', '.'));
        }

        $this->info("Monthly snapshot generated for " . $date->format('F Y'));
    }
}