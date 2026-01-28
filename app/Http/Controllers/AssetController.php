<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\MarketService; // Pastikan Service ini ada

class AssetController extends Controller
{
    protected $marketService;

    /**
     * Dependency Injection MarketService
     * Agar bisa memanggil fungsi fetchYahooData dan fetchCoinGeckoData
     */
    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    // =========================================================================
    // 1. INDEX: MENAMPILKAN DAFTAR ASET (ADMIN)
    // =========================================================================
    public function index(Request $request)
    {
        $query = Asset::query();

        // 1. Get 'type' from URL, default to 'Stock' if missing
        $type = $request->query('type', 'Stock');

        // 2. Filter assets by type
        $query = Asset::where('type', $type);

        // 3. Optional: Search feature
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('symbol', 'asc')->paginate(10);

        // 4. IMPORTANT: Pass 'type' to the view
        return view('admin.assets.index', compact('assets', 'type'));
    }

    // =========================================================================
    // 2. CREATE: FORM TAMBAH ASET BARU
    // =========================================================================
    public function create(Request $request)
    {
        // Ambil parameter type dari URL (misal: ?type=Stock)
        // Default ke 'Stock' jika tidak ada
        $type = $request->query('type', 'Stock');
        
        return view('admin.assets.create', compact('type'));
    }

    // =========================================================================
    // 3. STORE: PROSES SIMPAN KE DATABASE
    // =========================================================================
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'symbol' => 'required|unique:assets,symbol|max:10|uppercase',
            'name'   => 'required|string|max:255',
            'type'   => 'required|in:Stock,Crypto,Mutual Fund,Gold,Currency',
            'subtype' => 'nullable|in:RDPU,RDPT,RDS,Campuran',
            'current_price' => 'required|numeric|min:0',
            // Gunakan 'string' agar bisa menerima URL panjang atau Base64 Image
            'logo'   => 'nullable|string', 
            'api_id' => 'nullable|string', // Opsional: ID untuk CoinGecko
        ]);

        // Simpan Data
        Asset::create([
            'symbol' => strtoupper($request->symbol),
            'name'   => $request->name,
            'type'   => $request->type,
            'subtype' => $request->type === 'Mutual Fund' ? $request->subtype : null,
            'current_price' => $request->current_price,
            'logo'   => $request->logo,
            'api_id' => $request->api_id ?? null,
        ]);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil ditambahkan!');
    }

    // =========================================================================
    // 4. EDIT: FORM EDIT ASET
    // =========================================================================
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        // Type diambil langsung dari data aset tersebut
        $type = $asset->type;

        return view('admin.assets.edit', compact('asset', 'type'));
    }

    // =========================================================================
    // 5. UPDATE: PROSES UPDATE DATA UTAMA
    // =========================================================================
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            // Validasi unik, tapi abaikan ID aset ini sendiri
            'symbol' => ['required', 'max:10', 'uppercase', Rule::unique('assets')->ignore($asset->id)],
            'name'   => 'required|string|max:255',
            'type'   => 'required|in:Stock,Crypto,Mutual Fund,Gold,Currency',
            'subtype' => 'nullable|in:RDPU,RDPT,RDS,Campuran',
            'current_price' => 'required|numeric|min:0',
            'logo'   => 'nullable|string', // Support Base64
            'api_id' => 'nullable|string',
        ]);

        $asset->update([
            'symbol' => strtoupper($request->symbol),
            'name'   => $request->name,
            'type'   => $request->type,
            'subtype' => $request->type === 'Mutual Fund' ? $request->subtype : null,
            'current_price' => $request->current_price,
            'logo'   => $request->logo,
            'api_id' => $request->api_id ?? null,
        ]);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Data aset berhasil diperbarui!');
    }

    // =========================================================================
    // 6. UPDATE PRICE: UPDATE HARGA MANUAL (VIA MODAL)
    // =========================================================================
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric|min:0'
        ]);

        $asset = Asset::findOrFail($id);
        $asset->current_price = $request->current_price;
        $asset->touch(); // Update timestamp 'updated_at'
        $asset->save();

        return back()->with('success', 'Harga ' . $asset->symbol . ' berhasil diupdate!');
    }

    // =========================================================================
    // 7. SYNC PRICES: TOMBOL SYNC (UPDATE HARGA + LOGO OTOMATIS)
    // =========================================================================
    public function syncPrices()
    {
        // Ambil semua aset tipe Stock atau Crypto
        $assets = Asset::whereIn('type', ['Stock', 'Crypto'])->get();
        $updatedCount = 0;

        foreach ($assets as $asset) {
            $marketData = null;

            // --- A. LOGIKA CRYPTO (COINGECKO) ---
            if ($asset->type == 'Crypto') {
                // Prioritas 1: Gunakan kolom 'api_id' (misal: 'bitcoin')
                // Prioritas 2: Gunakan 'name' (misal: 'Bitcoin')
                $searchId = $asset->api_id ?: $asset->name; 
                
                // Ambil harga & logo dari CoinGecko
                $marketData = $this->marketService->fetchCoinGeckoData($searchId);
            } 
            
            // --- B. LOGIKA SAHAM (YAHOO FINANCE) ---
            elseif ($asset->type == 'Stock') {
                $symbol = $asset->symbol;
                
                // Tambahkan suffix .JK untuk saham Indonesia jika belum ada
                // Contoh: BBCA -> BBCA.JK
                if (!str_contains($symbol, '.')) {
                    $symbol .= '.JK';
                }
                
                // Ambil harga dari Yahoo
                $marketData = $this->marketService->fetchYahooData($symbol);
            }

            // --- C. PROSES SIMPAN KE DATABASE ---
            if ($marketData && $marketData['success']) {
                $asset->current_price = $marketData['price'];
                
                // Fitur Auto-Logo (Khusus Crypto):
                // Jika aset belum punya logo, dan API memberikan logo, simpan logonya.
                if ($asset->type == 'Crypto' && !empty($marketData['logo']) && empty($asset->logo)) {
                    $asset->logo = $marketData['logo'];
                }

                $asset->touch(); // Update timestamp 'updated_at'
                $asset->save();
                $updatedCount++;
            }
        }

        return back()->with('success', "Berhasil sinkronisasi {$updatedCount} aset (Harga & Logo).");
    }

    // =========================================================================
    // 8. DESTROY: HAPUS ASET
    // =========================================================================
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return back()->with('success', 'Aset berhasil dihapus.');
    }
}