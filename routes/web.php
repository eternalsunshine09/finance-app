<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;

// --- AREA UJI COBA (Nanti dihapus kalau sudah punya UI) ---

// 1. Test Top Up Rp 5.000.000
Route::get('/test-topup', function (TransactionController $controller) {
    // Kita bikin data pura-pura (Simulasi Input Form)
    $request = Request::create('/test-topup', 'POST', [
        'user_id' => 2,       // ID si Budi
        'amount' => 5000000,  // Top Up 5 Juta
        'currency' => 'IDR'
    ]);
    
    // Paksa aplikasi menerima ini sebagai JSON biar gak error redirect
    $request->headers->set('Accept', 'application/json');

    return $controller->topUp($request);
});

// 2. Test Beli Saham ANTM (Beli 100 lembar)
Route::get('/test-beli', function (TransactionController $controller) {
    // Simulasi Budi beli ANTM
    $request = Request::create('/test-beli', 'POST', [
        'user_id' => 2,
        'asset_symbol' => 'ANTM',
        'quantity' => 100,      // Beli 100 lembar
        'price_per_unit' => 2000 // Anggap harga lagi Rp 2.000/lembar
    ]);

    $request->headers->set('Accept', 'application/json');

    return $controller->buyAsset($request);
});