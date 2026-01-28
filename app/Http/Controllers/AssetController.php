<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\MarketService; // Pastikan file app/Services/MarketService.php ada

class AssetController extends Controller
{
    protected $marketService;

    /**
     * Dependency Injection MarketService
     * Untuk mengambil data harga real-time dari Yahoo Finance & CoinGecko
     */
    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    // =========================================================================
    // 1. INDEX: MENAMPILKAN DAFTAR ASET (ADMIN PAGE)
    // =========================================================================
    public function index(Request $request)
    {
        // 1. Ambil 'type' dari URL, default ke 'Stock' (Saham Indo)
        $type = $request->query('type', 'Stock');

        // 2. Query dasar
        $query = Asset::where('type', $type);

        // 3. Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        // 4. Pagination & Urutan
        $assets = $query->orderBy('symbol', 'asc')->paginate(10);

        // 5. Return View
        return view('admin.assets.index', compact('assets', 'type'));
    }

    // =========================================================================
    // 2. CREATE: FORM TAMBAH ASET BARU
    // =========================================================================
    public function create(Request $request)
    {
        $type = $request->query('type', 'Stock');
        return view('admin.assets.create', compact('type'));
    }

    // =========================================================================
    // 3. STORE: PROSES SIMPAN KE DATABASE
    // =========================================================================
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'symbol' => 'required|unique:assets,symbol|max:10', // Max 10 chars
            'name'   => 'required|string|max:255',
            // 🔥 PENTING: Tambahkan 'US Stock' ke dalam validasi 'in:'
            'type'   => 'required|in:Stock,US Stock,Crypto,Mutual Fund,Gold,Currency',
            'subtype' => 'nullable|string', // Untuk Reksadana (RDPU/RDS/dll)
            'current_price' => 'required|numeric|min:0',
            'logo'   => 'nullable|string', // Support URL logo
            'api_id' => 'nullable|string', // Opsional: ID CoinGecko
        ]);

        // 2. Simpan Data
        Asset::create([
            'symbol' => strtoupper($request->symbol), // Paksa huruf besar (AAPL)
            'name'   => $request->name,
            'type'   => $request->type,
            'subtype' => $request->type === 'Mutual Fund' ? $request->subtype : null,
            'current_price' => $request->current_price,
            'logo'   => $request->logo,
            'api_id' => $request->api_id ?? null,
            'change_percent' => 0 // Default 0 saat baru dibuat
        ]);

        // 3. Redirect
        return redirect()->route('admin.assets.index', ['type' => $request->type])
            ->with('success', 'Aset berhasil ditambahkan!');
    }

    // =========================================================================
    // 4. EDIT: FORM EDIT ASET
    // =========================================================================
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $type = $asset->type; // Ambil tipe langsung dari data aset
        return view('admin.assets.edit', compact('asset', 'type'));
    }

    // =========================================================================
    // 5. UPDATE: PROSES UPDATE DATA UTAMA
    // =========================================================================
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        // 1. Validasi
        $request->validate([
            // Validasi unik kecuali untuk ID diri sendiri
            'symbol' => ['required', 'max:10', Rule::unique('assets')->ignore($asset->id)],
            'name'   => 'required|string|max:255',
            'type'   => 'required|in:Stock,US Stock,Crypto,Mutual Fund,Gold,Currency',
            'current_price' => 'required|numeric|min:0',
            'logo'   => 'nullable|string',
        ]);

        // 2. Update Database
        $asset->update([
            'symbol' => strtoupper($request->symbol),
            'name'   => $request->name,
            'type'   => $request->type,
            'subtype' => $request->type === 'Mutual Fund' ? $request->subtype : $asset->subtype,
            'current_price' => $request->current_price,
            'logo'   => $request->logo,
            'api_id' => $request->api_id ?? $asset->api_id,
        ]);

        return redirect()->route('admin.assets.index', ['type' => $asset->type])
            ->with('success', 'Data aset berhasil diperbarui!');
    }

    // =========================================================================
    // 6. UPDATE PRICE: UPDATE HARGA MANUAL (VIA MODAL ADMIN)
    // =========================================================================
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric|min:0'
        ]);

        $asset = Asset::findOrFail($id);
        $asset->current_price = $request->current_price;
        $asset->save();

        return back()->with('success', 'Harga ' . $asset->symbol . ' berhasil diupdate!');
    }

    // =========================================================================
    // 7. SYNC PRICES: TOMBOL SYNC (UPDATE HARGA OTOMATIS)
    // =========================================================================
    public function syncPrices()
    {
        // Ambil semua aset tipe Stock, US Stock, atau Crypto
        $assets = Asset::whereIn('type', ['Stock', 'US Stock', 'Crypto'])->get();
        $updatedCount = 0;

        foreach ($assets as $asset) {
            $marketData = null;

            // --- A. CRYPTO (CoinGecko) ---
            if ($asset->type == 'Crypto') {
                // Prioritas: api_id -> name (lowercase)
                $searchId = $asset->api_id ?: strtolower($asset->name); 
                $marketData = $this->marketService->fetchCoinGeckoData($searchId);
            } 
            
            // --- B. SAHAM INDONESIA (Yahoo: .JK) ---
            elseif ($asset->type == 'Stock') {
                $symbol = $asset->symbol;
                // Tambahkan .JK jika belum ada
                if (!str_contains($symbol, '.')) {
                    $symbol .= '.JK';
                }
                $marketData = $this->marketService->fetchYahooData($symbol);
            }

            // --- C. SAHAM AMERIKA (Yahoo: Polos) ---
            // 🔥 FITUR BARU: Sync Harga US Stock 🔥
            elseif ($asset->type == 'US Stock') {
                $symbol = $asset->symbol; // Contoh: AAPL, TSLA (Tanpa suffix)
                $marketData = $this->marketService->fetchYahooData($symbol);
            }

            // --- D. EMAS (GOLD) - KHUSUS PEGADAIAN/ANTAM ---
            // Jika simbolnya 'GOLD', kita ambil harga dunia konversi IDR
            elseif ($asset->type == 'Gold' && strtoupper($asset->symbol) == 'GOLD') {
                $marketData = $this->marketService->getGoldPriceIdr('1d');
            }

            // --- E. PROSES SIMPAN ---
            if ($marketData && $marketData['success']) {
                $asset->current_price = $marketData['price'];
                $asset->change_percent = $marketData['change_percent'] ?? 0;
                
                // Auto-Logo Khusus Crypto (Jika kosong)
                if ($asset->type == 'Crypto' && !empty($marketData['logo']) && empty($asset->logo)) {
                    $asset->logo = $marketData['logo'];
                }

                $asset->touch(); // Update timestamp
                $asset->save();
                $updatedCount++;
            }
        }

        return back()->with('success', "Berhasil sinkronisasi harga untuk {$updatedCount} aset.");
    }

    // =========================================================================
    // 8. DESTROY: HAPUS ASET
    // =========================================================================
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $type = $asset->type; // Simpan tipe untuk redirect
        $asset->delete();
        
        return redirect()->route('admin.assets.index', ['type' => $type])
                         ->with('success', 'Aset berhasil dihapus.');
    }
}