<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        // Field Umum
        'symbol',
        'name',
        'type',
        'current_price',
        'price_updated_at',
        'logo_url',  // Ganti dari 'logo' ke 'logo_url'
        'notes',
        
        // Field untuk semua tipe
        'api_id',
        
        // Field untuk Mutual Fund
        'management_fee',
        'minimum_purchase',
        'mutual_fund_type',  // Simpan langsung sebagai mutual_fund_type
        'risk_level',
        'investment_manager',
        'manager_website',
        'launch_date',
        'category',
        
        // Field untuk US Stock
        'sector',
        'exchange',
        'market_cap',
        'country',
        'company_website',
        'ceo',
        
        // Field untuk Saham Indonesia
        'subtype',  // Tetap ada untuk kompatibilitas
    ];

    // Scope untuk tipe tertentu
    public function scopeMutualFund($query)
    {
        return $query->where('type', 'Mutual Fund');
    }
    
    public function scopeUsStock($query)
    {
        return $query->where('type', 'US Stock');
    }
    
    public function scopeStock($query)
    {
        return $query->where('type', 'Stock');
    }
    
    public function scopeCrypto($query)
    {
        return $query->where('type', 'Crypto');
    }
}