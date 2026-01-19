<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // 🔥 TAMBAHKAN INI AGAR DATA BISA DISIMPAN 🔥
    protected $guarded = ['id']; 

    // Hapus atau komentari protected $fillable jika ada, 
    // karena $guarded = ['id'] sudah otomatis mengizinkan semua kolom selain ID.

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}