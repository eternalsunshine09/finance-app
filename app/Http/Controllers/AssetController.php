<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    // 1. Tampilkan Daftar Aset
    public function index()
    {
        $assets = Asset::all();
        return view('admin.assets.index', compact('assets'));
    }

    // 2. Simpan Aset Baru
    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|unique:assets,symbol|uppercase|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:stock,crypto',
            'current_price' => 'required|numeric',
        ]);

        Asset::create($request->all());

        return redirect()->back()->with('success', 'Aset baru berhasil ditambahkan!');
    }

    // 3. Update Harga Aset
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update(['current_price' => $request->current_price]);

        return redirect()->back()->with('success', 'Harga berhasil diupdate!');
    }

    // 4. Hapus Aset
    public function destroy($id)
    {
        Asset::destroy($id);
        return redirect()->back()->with('success', 'Aset dihapus!');
    }
}