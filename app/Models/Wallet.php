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
        'currency',
        'bank_name',      // Baru
        'account_name',   // Baru
        'account_number'  // Baru
    ];

    // Dompet ini milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}