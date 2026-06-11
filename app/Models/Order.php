<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Kolom yang diizinkan untuk pengisian massal (Mass Assignment)
    protected $fillable = [
        'user_id',
        'produk_id',
        'jumlah',
        'total_harga',
        'metode_pembayaran',
        'status',
    ];

    // Relasi: Satu order dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Satu order berisi satu produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Relasi: Satu order bisa memiliki satu rating/ulasan
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}