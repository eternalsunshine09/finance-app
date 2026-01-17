<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. AREA TAMU (Belum Login) ---
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    // LOGIN
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // REGISTER (Baru Ditambahkan)
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

// --- 2. AREA MEMBER (Wajib Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =================================================================
    //  AREA UJI COBA TRANSAKSI (DEVELOPER ONLY)
    //  (Nanti dihapus kalau form input UI sudah jadi)
    // =================================================================

    // FITUR TOP UP
    Route::get('/topup', [TransactionController::class, 'showTopUpForm'])->name('topup');
    Route::post('/topup', [TransactionController::class, 'topUp'])->name('topup.process');

    // Test 2: Beli Saham
    // FITUR BELI ASET
    Route::get('/buy', [TransactionController::class, 'showBuyForm'])->name('buy');
    Route::post('/buy', [TransactionController::class, 'buyAsset'])->name('buy.process');
    // Test 3: Jual Saham ANTM
    Route::get('/test-jual', function (TransactionController $controller) {
        $userId = Auth::id();

        $request = Request::create('/test-jual', 'POST', [
            'user_id' => $userId,
            'asset_symbol' => 'ANTM',
            'quantity' => 50,       // Jual 50 lembar
            'price_per_unit' => 2500 
        ]);
        
        $request->headers->set('Accept', 'application/json');
        return $controller->sellAsset($request);
    });

    // --- 3. AREA ADMIN (Hanya Role 'admin') ---
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        
        // A. Dashboard Admin
        Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // B. Manajemen Aset
        Route::get('/assets', [App\Http\Controllers\AssetController::class, 'index'])->name('admin.assets.index');
        Route::post('/assets', [App\Http\Controllers\AssetController::class, 'store'])->name('admin.assets.store');
        Route::patch('/assets/{id}', [App\Http\Controllers\AssetController::class, 'updatePrice'])->name('admin.assets.updatePrice');
        Route::delete('/assets/{id}', [App\Http\Controllers\AssetController::class, 'destroy'])->name('admin.assets.destroy');

        // C. Approval Top Up (INI YANG TADI HILANG/ERROR) 👇
        Route::get('/topups', [App\Http\Controllers\AdminTransactionController::class, 'index'])->name('admin.transactions.index');
        Route::patch('/topups/{id}/approve', [App\Http\Controllers\AdminTransactionController::class, 'approve'])->name('admin.transactions.approve');
        Route::patch('/topups/{id}/reject', [App\Http\Controllers\AdminTransactionController::class, 'reject'])->name('admin.transactions.reject');
    });

    // Test 4: Tarik Dana (Withdraw)
    Route::get('/test-tarik', function (TransactionController $controller) {
        $userId = Auth::id();

        $request = Request::create('/test-tarik', 'POST', [
            'user_id' => $userId,
            'amount' => 1000000, // Tarik 1 Juta
            'currency' => 'IDR'
        ]);
        
        $request->headers->set('Accept', 'application/json');
        return $controller->withdraw($request);
    });
    // FITUR HISTORY
    Route::get('/history', [TransactionController::class, 'history'])->name('history');
});