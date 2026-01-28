<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AssetController; // Admin Asset Controller
use App\Http\Controllers\AdminTransactionController; // Admin Transaction Controller
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminExchangeRateController;

// ==========================================
// PUBLIC ROUTES
// ==========================================
Route::get('/', function () {
    $assets = \App\Models\Asset::take(5)->get(); 
    return view('welcome', compact('assets'));
})->name('welcome');

// ==========================================
// GUEST ROUTES (Login/Register)
// ==========================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// ==========================================
// MEMBER ROUTES (User Dashboard & Actions)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Auth
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Transactions (Topup, Withdraw, Buy, Sell)
    Route::get('/topup', [TransactionController::class, 'showTopUpForm'])->name('topup');
    Route::post('/topup', [TransactionController::class, 'topUp'])->name('topup.process');
    Route::get('/withdraw', [TransactionController::class, 'showWithdrawForm'])->name('withdraw');
    Route::post('/withdraw', [TransactionController::class, 'withdraw'])->name('withdraw.process');
    Route::get('/buy', [TransactionController::class, 'showBuyForm'])->name('buy');
    Route::post('/buy', [TransactionController::class, 'processBuy'])->name('buy.process');
    Route::get('/sell/{symbol?}', [TransactionController::class, 'sell'])->name('sell');
    Route::post('/sell/process', [TransactionController::class, 'processSell'])->name('sell.process');
    
    // Transaction History
    Route::get('/history', [TransactionController::class, 'history'])->name('history');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Corporate Actions
    Route::prefix('transactions')->name('transactions.')->group(function() {
        Route::get('/dividend-cash', [TransactionController::class, 'formDividendCash'])->name('dividend.cash');
        Route::get('/dividend-unit', [TransactionController::class, 'formDividendUnit'])->name('dividend.unit');
        Route::get('/stock-split', [TransactionController::class, 'formStockSplit'])->name('stocksplit');
        Route::get('/right-issue', [TransactionController::class, 'formRightIssue'])->name('rightissue');
        Route::get('/bonus', [TransactionController::class, 'formBonus'])->name('bonus');
        Route::post('/process-corporate-action', [TransactionController::class, 'processCorporateAction'])->name('process_ca');
    });

    // Exchange
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');
    Route::post('/exchange', [ExchangeController::class, 'process'])->name('exchange.process');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [WalletController::class, 'store'])->name('wallet.store');
    // Route::get('/wallet/history', [WalletController::class, 'history'])->name('wallet.history'); // Removed as discussed
    Route::get('/wallet/{id}', [WalletController::class, 'show'])->name('wallet.show');
    Route::get('/wallet/{id}/edit', [WalletController::class, 'edit'])->name('wallet.edit');
    Route::put('/wallet/{id}', [WalletController::class, 'update'])->name('wallet.update');
    Route::delete('/wallet/{id}', [WalletController::class, 'destroy'])->name('wallet.destroy');

    // Market Routes (Verified Only)
    Route::middleware(['verified'])->name('market.')->prefix('market')->group(function () {
        Route::get('/indonesia', [MarketController::class, 'index'])->name('index');
        Route::get('/us', [MarketController::class, 'us'])->name('us');
        Route::get('/crypto', [MarketController::class, 'crypto'])->name('crypto');
        Route::get('/commodities', [MarketController::class, 'commodities'])->name('commodities');
        Route::get('/reksadana', [MarketController::class, 'reksadana'])->name('reksadana');
    });
    
    // Portfolio
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

    // Internal APIs
    Route::get('/api/price/{symbol}', function ($symbol) {
        $asset = \App\Models\Asset::where('symbol', $symbol)->first();
        return response()->json(['price' => $asset ? $asset->current_price : 0]);
    })->name('api.price');
    
    Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('api.chart');
});

// ==========================================
// ADMIN ROUTES (Middleware & Prefix)
// ==========================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. USER MANAGEMENT
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');

    // 3. MASTER ASSETS (CRUD + Custom Actions)
    Route::prefix('assets')->name('assets.')->group(function() {
        // List Assets
        Route::get('/', [AssetController::class, 'index'])->name('index');
        
        // Create Asset
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        
        // Edit Asset
        Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AssetController::class, 'update'])->name('update');
        
        // Delete Asset
        Route::delete('/{id}', [AssetController::class, 'destroy'])->name('destroy');

        // Custom Actions (Update Price & Sync)
        Route::post('/{id}/update-price', [AssetController::class, 'updatePrice'])->name('update_price'); 
        Route::post('/sync', [AssetController::class, 'syncPrices'])->name('sync'); 
    });

    // 4. EXCHANGE RATES MANAGEMENT
    Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
        Route::get('/', [AdminExchangeRateController::class, 'index'])->name('index');
        Route::post('/', [AdminExchangeRateController::class, 'store'])->name('store');
        Route::post('/sync', [AdminExchangeRateController::class, 'syncApi'])->name('sync');
        Route::delete('/{currency}', [AdminExchangeRateController::class, 'destroy'])->name('destroy');
    });

    // 5. TRANSACTION APPROVALS (Top Up / Buy / Sell) - FIXING THE ERROR HERE
    Route::prefix('transactions')->name('transactions.')->group(function() {
        Route::get('/', [AdminTransactionController::class, 'index'])->name('index'); // This defines admin.transactions.index
        Route::patch('/{id}/approve', [AdminTransactionController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject', [AdminTransactionController::class, 'reject'])->name('reject');
    });

    // 6. WITHDRAWAL APPROVALS
    Route::prefix('withdrawals')->name('withdrawals.')->group(function() {
        Route::get('/', [AdminTransactionController::class, 'indexWithdrawals'])->name('index');
        Route::patch('/{id}/approve', [AdminTransactionController::class, 'approveWithdraw'])->name('approve');
        Route::patch('/{id}/reject', [AdminTransactionController::class, 'rejectWithdraw'])->name('reject');
    });
});