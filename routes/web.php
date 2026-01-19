<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAssetController;
use App\Http\Controllers\AdminTransactionController;

// --- 1. AREA TAMU (Guest) ---
Route::middleware(['guest'])->group(function () {
   Route::get('/', function () {
        // Ambil data aset acak buat hiasan ticker harga di depan
        $assets = \App\Models\Asset::take(5)->get(); 
        return view('welcome', compact('assets')); 
    });
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// --- 2. AREA MEMBER (Auth) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/api/price/{symbol}', [App\Http\Controllers\TransactionController::class, 'getPrice']);
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // ... di dalam group auth ...

    // FITUR PROFIL
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Fitur Top Up
    Route::get('/topup', [TransactionController::class, 'showTopUpForm'])->name('topup');
    Route::post('/topup', [TransactionController::class, 'topUp'])->name('topup.process');

    // Fitur Withdraw
    Route::get('/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw');
    Route::post('/withdraw', [TransactionController::class, 'withdraw'])->name('withdraw.process');

    // Fitur Beli
    Route::get('/buy', [TransactionController::class, 'showBuyForm'])->name('buy');
    Route::post('/buy', [App\Http\Controllers\TransactionController::class, 'processBuy'])->name('buy.process');

    // Fitur Jual (YANG TADI HILANG)
    Route::get('/sell/{symbol}', [TransactionController::class, 'showSellForm'])->name('sell');
    Route::post('/sell', [TransactionController::class, 'sellAsset'])->name('sell.process');

    // Fitur History
    Route::get('/history', [TransactionController::class, 'history'])->name('history');

    // Fitur Exchange
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');
    Route::post('/exchange', [ExchangeController::class, 'process'])->name('exchange.process');

    // Wallet Index & Store
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [App\Http\Controllers\WalletController::class, 'store'])->name('wallet.store');
    
    // Halaman History Terpisah
    Route::get('/wallet/history', [App\Http\Controllers\WalletController::class, 'history'])->name('wallet.history');
    
    // Halaman Edit & Update & Destroy
    Route::resource('wallet', App\Http\Controllers\WalletController::class);
    Route::get('/wallet/{id}/edit', [App\Http\Controllers\WalletController::class, 'edit'])->name('wallet.edit');
    Route::put('/wallet/{id}', [App\Http\Controllers\WalletController::class, 'update'])->name('wallet.update');
    Route::delete('/wallet/{id}', [App\Http\Controllers\WalletController::class, 'destroy'])->name('wallet.destroy');


    // MENU MARKET / EXCHANGE
    Route::get('/market', [App\Http\Controllers\MarketController::class, 'index'])->name('market.index');

    // MENU PORTOFOLIO
    Route::get('/portfolio', [App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.index');

    // 👇 API INTERNAL: Untuk ambil harga aset via Javascript
    Route::get('/api/price/{symbol}', function ($symbol) {
        $asset = App\Models\Asset::where('symbol', $symbol)->first();
        return response()->json([
            'price' => $asset ? $asset->current_price : 0
        ]);
    })->name('api.price');
});

// GROUP ROUTE KHUSUS ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [App\Http\Controllers\AdminUserController::class, 'show'])->name('users.show'); // Detail User

    // Master Aset (CRUD)
    Route::get('/assets', [AdminAssetController::class, 'index'])->name('assets.index');
    Route::post('/assets/sync', [App\Http\Controllers\AdminAssetController::class, 'syncPrices'])->name('assets.sync');
    Route::get('/assets/create', [App\Http\Controllers\AdminAssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AdminAssetController::class, 'store'])->name('assets.store');
    Route::patch('/assets/{id}/price', [AdminAssetController::class, 'updatePrice'])->name('assets.updatePrice');
    Route::post('/assets/sync', [AdminAssetController::class, 'sync'])->name('assets.sync');
    Route::delete('/assets/{id}', [AdminAssetController::class, 'destroy'])->name('assets.destroy');

    // Fitur Kelola Kurs (Exchange Rate)
    Route::post('/exchange-rate/update', [App\Http\Controllers\AdminAssetController::class, 'updateRate'])->name('exchange.update');
    Route::post('/exchange-rate/sync-api', [App\Http\Controllers\AdminAssetController::class, 'syncRateAPI'])->name('exchange.syncApi');

    // KELOLA VALAS (Admin Exchange Rates)
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminExchangeRateController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\AdminExchangeRateController::class, 'store'])->name('store');
        Route::post('/sync', [App\Http\Controllers\AdminExchangeRateController::class, 'syncApi'])->name('sync');
        Route::delete('/{currency}', [App\Http\Controllers\AdminExchangeRateController::class, 'destroy'])->name('destroy');
    });

    // Transaksi (Top Up)
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::patch('/transactions/{id}/approve', [AdminTransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('/transactions/{id}/reject', [AdminTransactionController::class, 'reject'])->name('transactions.reject');

    // Transaksi (Withdraw)
    Route::get('/withdrawals', [AdminTransactionController::class, 'indexWithdrawals'])->name('withdrawals.index');
    Route::patch('/withdrawals/{id}/approve', [AdminTransactionController::class, 'approveWithdraw'])->name('withdrawals.approve');
    Route::patch('/withdrawals/{id}/reject', [AdminTransactionController::class, 'rejectWithdraw'])->name('withdrawals.reject');
});