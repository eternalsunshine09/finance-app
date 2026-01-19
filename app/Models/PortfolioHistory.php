<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'total_value'];

    // Agar kolom date dibaca sebagai object Carbon (bisa diformat tgl-bln-thn)
    protected $casts = [
        'date' => 'date',
    ];
}