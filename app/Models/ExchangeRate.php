<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    // Tambahkan 'date' ke dalam fillable
    protected $fillable = [
        'from_currency', 
        'to_currency', 
        'rate', 
        'date' // 🔥 Wajib ada agar controller bisa menyimpan tanggal
    ];
}