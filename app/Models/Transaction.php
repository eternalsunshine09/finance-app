<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Transaksi milik user siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Transaksi pakai dompet apa
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}