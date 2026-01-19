<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    // 🔥 Pastikan 'average_buy_price' ada di sini
    protected $fillable = [
        'user_id',
        'asset_symbol',
        'quantity',
        'average_buy_price' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function asset()
    {
        // Pastikan relasi ini benar (asset_symbol ke symbol)
        return $this->belongsTo(Asset::class, 'asset_symbol', 'symbol');
    }
}