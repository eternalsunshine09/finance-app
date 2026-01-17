<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminDashboardController;

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
    Route::post('/buy', [TransactionController::class, 'buyAsset'])->name('buy.process');

    // Fitur Jual (YANG TADI HILANG)
    Route::get('/sell/{symbol}', [TransactionController::class, 'showSellForm'])->name('sell');
    Route::post('/sell', [TransactionController::class, 'sellAsset'])->name('sell.process');

    // Fitur History
    Route::get('/history', [TransactionController::class, 'history'])->name('history');

    // MENU WALLET
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');

    // MENU MARKET / EXCHANGE
    Route::get('/market', [App\Http\Controllers\MarketController::class, 'index'])->name('market.index');

    // 👇 API INTERNAL: Untuk ambil harga aset via Javascript
    Route::get('/api/price/{symbol}', function ($symbol) {
        $asset = App\Models\Asset::where('symbol', $symbol)->first();
        return response()->json([
            'price' => $asset ? $asset->current_price : 0
        ]);
    })->name('api.price');
});

// --- 3. AREA ADMIN ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Aset
    // SYNC HARGA (Taruh sebelum route /{id} biar gak bentrok)
    Route::post('/assets/sync', [App\Http\Controllers\AssetController::class, 'syncToApi'])->name('admin.assets.sync');
    Route::get('/assets', [AssetController::class, 'index'])->name('admin.assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('admin.assets.store');
    Route::patch('/assets/{id}', [AssetController::class, 'updatePrice'])->name('admin.assets.updatePrice');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('admin.assets.destroy');

    // Approval
    Route::get('/topups', [AdminTransactionController::class, 'index'])->name('admin.transactions.index');
    Route::patch('/topups/{id}/approve', [AdminTransactionController::class, 'approve'])->name('admin.transactions.approve');
    Route::patch('/topups/{id}/reject', [AdminTransactionController::class, 'reject'])->name('admin.transactions.reject');
    
    // --- 3. AREA ADMIN (Tambahkan di grup 'admin') ---
    // Approval Withdraw
    Route::get('/withdrawals', [AdminTransactionController::class, 'indexWithdrawals'])->name('admin.withdrawals.index');
    Route::patch('/withdrawals/{id}/approve', [AdminTransactionController::class, 'approveWithdraw'])->name('admin.withdrawals.approve');
    Route::patch('/withdrawals/{id}/reject', [AdminTransactionController::class, 'rejectWithdraw'])->name('admin.withdrawals.reject');
});