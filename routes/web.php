<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
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
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
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

    // Test 1: Top Up Saldo
    Route::get('/test-topup', function (TransactionController $controller) {
        $userId = Auth::id(); // Ambil ID user yang sedang login

        $request = Request::create('/test-topup', 'POST', [
            'user_id' => $userId,
            'amount' => 5000000, // Topup 5 Juta
            'currency' => 'IDR'
        ]);
        
        $request->headers->set('Accept', 'application/json');
        return $controller->topUp($request);
    });

    // Test 2: Beli Saham ANTM
    Route::get('/test-beli', function (TransactionController $controller) {
        $userId = Auth::id();

        $request = Request::create('/test-beli', 'POST', [
            'user_id' => $userId,
            'asset_symbol' => 'ANTM',
            'quantity' => 100,      // Beli 100 lembar
            'price_per_unit' => 2000 
        ]);

        $request->headers->set('Accept', 'application/json');
        return $controller->buyAsset($request);
    });

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
});