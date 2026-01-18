<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    
    // Izinkan semua kolom diisi (shortcut biar gak ribet ngetik fillable satu-satu)
    protected $fillable = [
        'user_id', 
        'balance', 
        'currency',       // 👈 Pastikan ini ada
        'bank_name',      // 👈 Ini juga baru
        'account_name',   // 👈 Ini juga baru
        'account_number'  // 👈 Dan ini
    ];

    // Dompet ini milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}