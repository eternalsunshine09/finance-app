<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class MarketController extends Controller
{
    public function index()
    {
        // Ambil semua aset, urutkan: Crypto dulu, baru Stock
        $assets = Asset::orderBy('type', 'asc')->get();

        return view('market.index', compact('assets'));
    }
}