<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MarketController; // <- Tambahkan ini
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAssetController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminExchangeRateController;

// Public Routes
Route::get('/', function () {
    $assets = \App\Models\Asset::take(5)->get(); 
    return view('welcome', compact('assets'));
})->name('welcome');

// Guest Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// Auth Member Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Transactions
    Route::get('/topup', [TransactionController::class, 'showTopUpForm'])->name('topup');
    Route::post('/topup', [TransactionController::class, 'topUp'])->name('topup.process');
    Route::get('/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw');
    Route::post('/withdraw', [TransactionController::class, 'withdraw'])->name('withdraw.process');
    Route::get('/buy', [TransactionController::class, 'showBuyForm'])->name('buy');
    Route::post('/buy', [TransactionController::class, 'processBuy'])->name('buy.process');
    Route::get('/sell/{symbol?}', [TransactionController::class, 'sell'])->name('sell');
    Route::post('/sell/process', [TransactionController::class, 'processSell'])->name('sell.process');
    Route::get('/history', [TransactionController::class, 'history'])->name('history');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Exchange
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');
    Route::post('/exchange', [ExchangeController::class, 'process'])->name('exchange.process');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [WalletController::class, 'store'])->name('wallet.store');
    Route::get('/wallet/history', [WalletController::class, 'history'])->name('wallet.history');
    Route::get('/wallet/{id}', [WalletController::class, 'show'])->name('wallet.show');
    Route::get('/wallet/{id}/edit', [WalletController::class, 'edit'])->name('wallet.edit');
    Route::put('/wallet/{id}', [WalletController::class, 'update'])->name('wallet.update');
    Route::delete('/wallet/{id}', [WalletController::class, 'destroy'])->name('wallet.destroy');

    // Market Routes
    Route::middleware(['auth', 'verified'])->group(function () {
        // Indonesia Market
        Route::get('/market/indonesia', [MarketController::class, 'index'])
            ->name('market.index');
        
        // US Market  
        Route::get('/market/us', [MarketController::class, 'us'])
            ->name('market.us');
        
        // Crypto Market
        Route::get('/market/crypto', [MarketController::class, 'crypto'])
            ->name('market.crypto');
        
        // Commodities Market
        Route::get('/market/commodities', [MarketController::class, 'commodities'])
            ->name('market.commodities');
        
        // Reksadana Market
        Route::get('/market/reksadana', [MarketController::class, 'reksadana'])->name('market.reksadana');
    });
    
    // Portfolio
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

    // API
    Route::get('/api/price/{symbol}', function ($symbol) {
        $asset = \App\Models\Asset::where('symbol', $symbol)->first();
        return response()->json([
            'price' => $asset ? $asset->current_price : 0
        ]);
    })->name('api.price');
    
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('api.chart');
});

// ====== ADMIN ROUTES ======
// Note: Untuk admin assets, kita tetap gunakan AssetController khusus admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');

    // ====== ADMIN ASSETS (CRUD) ======
    // Pastikan ini mengarah ke AssetController yang kita buat untuk admin
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    Route::patch('/assets/{id}/price', [AssetController::class, 'updatePrice'])->name('assets.updatePrice');
    Route::post('/assets/sync-crypto', [AssetController::class, 'syncCryptoPrices'])->name('assets.syncCrypto');
    Route::post('/assets/sync-all', [AssetController::class, 'syncAllPrices'])->name('assets.syncAll');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Exchange Rates (tambahkan jika perlu)
    Route::post('/exchange-rate/update', [AssetController::class, 'updateRate'])->name('exchange.update');
    Route::post('/exchange-rate/sync-api', [AssetController::class, 'syncRateAPI'])->name('exchange.syncApi');

    // Admin Exchange Rates (jika ada controller terpisah)
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
        Route::get('/', [AdminExchangeRateController::class, 'index'])->name('index');
        Route::post('/', [AdminExchangeRateController::class, 'store'])->name('store');
        Route::post('/sync', [AdminExchangeRateController::class, 'syncApi'])->name('sync');
        Route::delete('/{currency}', [AdminExchangeRateController::class, 'destroy'])->name('destroy');
    });

    // Transactions
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::patch('/transactions/{id}/approve', [AdminTransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('/transactions/{id}/reject', [AdminTransactionController::class, 'reject'])->name('transactions.reject');

    // Withdrawals
    Route::get('/withdrawals', [AdminTransactionController::class, 'indexWithdrawals'])->name('withdrawals.index');
    Route::patch('/withdrawals/{id}/approve', [AdminTransactionController::class, 'approveWithdraw'])->name('withdrawals.approve');
    Route::patch('/withdrawals/{id}/reject', [AdminTransactionController::class, 'rejectWithdraw'])->name('withdrawals.reject');
});