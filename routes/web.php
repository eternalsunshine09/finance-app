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
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminExchangeRateController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Bisa diakses Siapa Saja)
|--------------------------------------------------------------------------
| Route '/' ditaruh di luar middleware agar menjadi halaman pertama
| yang muncul saat website dibuka, entah user sudah login atau belum.
*/

Route::get('/', function () {
    // Ambil data aset acak buat hiasan ticker harga di depan
    $assets = \App\Models\Asset::take(5)->get(); 
    return view('welcome', compact('assets')); 
})->name('welcome');


/*
|--------------------------------------------------------------------------
| 2. GUEST ROUTES (Hanya untuk yang BELUM Login)
|--------------------------------------------------------------------------
| Jika user sudah login dan mencoba akses ini, akan dilempar ke Dashboard
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});


/*
|--------------------------------------------------------------------------
| 3. AUTH MEMBER ROUTES (Hanya untuk Member Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // FITUR PROFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Fitur Top Up
    Route::get('/topup', [TransactionController::class, 'showTopUpForm'])->name('topup');
    Route::post('/topup', [TransactionController::class, 'topUp'])->name('topup.process');

    // Fitur Withdraw
    Route::get('/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw');
    Route::post('/withdraw', [TransactionController::class, 'withdraw'])->name('withdraw.process');

    // Fitur Beli
    Route::get('/buy', [TransactionController::class, 'showBuyForm'])->name('buy');
    Route::post('/buy', [TransactionController::class, 'processBuy'])->name('buy.process');

    // Fitur Jual
    Route::get('/sell/{symbol}', [TransactionController::class, 'showSellForm'])->name('sell');
    Route::post('/sell', [TransactionController::class, 'sellAsset'])->name('sell.process');

    // Fitur History
    Route::get('/history', [TransactionController::class, 'history'])->name('history');
    // === Route untuk Edit & Hapus Transaksi ===
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Fitur Exchange
   // Route untuk Halaman & Proses Konversi
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');
    Route::post('/exchange', [ExchangeController::class, 'process'])->name('exchange.process');

    // Wallet Index & Store
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [WalletController::class, 'store'])->name('wallet.store');
    

    // Halaman History Wallet (WAJIB DITARUH DI ATAS {id})
    Route::get('/wallet/history', [WalletController::class, 'history'])->name('wallet.history');
    
    // --- TAMBAHKAN BARIS INI (Rute Show/Detail) ---
    Route::get('/wallet/{id}', [WalletController::class, 'show'])->name('wallet.show');

    // Wallet Resource (Edit, Update, Destroy)
    Route::get('/wallet/{id}/edit', [WalletController::class, 'edit'])->name('wallet.edit');
    Route::put('/wallet/{id}', [WalletController::class, 'update'])->name('wallet.update');
    Route::delete('/wallet/{id}', [WalletController::class, 'destroy'])->name('wallet.destroy');

    // MENU MARKET / EXCHANGE
    Route::get('/market', [MarketController::class, 'index'])->name('market.index');

    // MENU PORTOFOLIO
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

    // API INTERNAL: Untuk ambil harga aset via Javascript
    Route::get('/api/price/{symbol}', function ($symbol) {
        $asset = \App\Models\Asset::where('symbol', $symbol)->first();
        return response()->json([
            'price' => $asset ? $asset->current_price : 0
        ]);
    })->name('api.price');
    // API INTERNAL: Untuk ambil data chart via Javascript
        Route::get('/api/chart-data', [App\Http\Controllers\DashboardController::class, 'getChartData'])->name('api.chart');
});


/*
|--------------------------------------------------------------------------
| 4. ADMIN ROUTES (Hanya untuk Role Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show'); 

    // Master Aset (CRUD)
    Route::get('/assets', [AdminAssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AdminAssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AdminAssetController::class, 'store'])->name('assets.store');
    Route::patch('/assets/{id}/price', [AdminAssetController::class, 'updatePrice'])->name('assets.updatePrice');
    Route::post('/assets/sync', [AdminAssetController::class, 'syncPrices'])->name('assets.sync'); // Sync Prices
    Route::post('/admin/assets/sync', [AdminAssetController::class, 'syncPrices'])->name('admin.assets.sync');
    Route::delete('/assets/{id}', [AdminAssetController::class, 'destroy'])->name('assets.destroy');

    // Fitur Kelola Kurs (Exchange Rate)
    Route::post('/exchange-rate/update', [AdminAssetController::class, 'updateRate'])->name('exchange.update');
    Route::post('/exchange-rate/sync-api', [AdminAssetController::class, 'syncRateAPI'])->name('exchange.syncApi');

    // KELOLA VALAS (Admin Exchange Rates)
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
        Route::get('/', [AdminExchangeRateController::class, 'index'])->name('index');
        Route::post('/', [AdminExchangeRateController::class, 'store'])->name('store');
        Route::post('/sync', [AdminExchangeRateController::class, 'syncApi'])->name('sync');
        Route::delete('/{currency}', [AdminExchangeRateController::class, 'destroy'])->name('destroy');
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