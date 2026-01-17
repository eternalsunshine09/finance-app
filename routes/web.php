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
    Route::get('/', function () { return redirect()->route('login'); });
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
});

// --- 3. AREA ADMIN ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Aset
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