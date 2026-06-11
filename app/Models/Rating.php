<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'user_id', 
        'produk_id', 
        'order_id', 
        'bintang', 
        'komentar'
    ];

    // Relasi: Rating ini diberikan oleh siapa (User)
    public function user()   
    { 
        return $this->belongsTo(User::class); 
    }

    // Relasi: Rating ini ditujukan untuk produk apa
    public function produk() 
    { 
        return $this->belongsTo(Produk::class); 
    }

    // Relasi: Rating ini berasal dari transaksi/order yang mana
    public function order()  
    { 
        return $this->belongsTo(Order::class); 
    }
}