<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapak extends Model
{
    use HasFactory; // Menambahkan trait HasFactory agar bisa menggunakan fitur Seeder/Factory bawaan Laravel

    protected $fillable = [
        'user_id',
        'nama_toko',
        'deskripsi_toko',
        'foto_toko',
        'kontak',
        'status'
    ];

    // Relasi: Lapak ini dimiliki oleh satu user
    public function user()    
    { 
        return $this->belongsTo(User::class); 
    }

    // Relasi: Lapak ini memiliki banyak produk yang dijual
    public function produks() 
    { 
        return $this->hasMany(Produk::class); 
    }
}