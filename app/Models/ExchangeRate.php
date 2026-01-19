<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    // Tambahkan semua kolom ini agar bisa diisi:
    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'date'
    ];
}