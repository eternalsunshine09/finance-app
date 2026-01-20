<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    // 👇 TAMBAHKAN BAGIAN INI 👇
    protected $fillable = [
        'symbol',
        'logo',
        'name',
        'type',
        'subtype',
        'current_price',
        'api_id' // <--- Jangan lupa kolom baru ini juga dimasukkan
    ];
}