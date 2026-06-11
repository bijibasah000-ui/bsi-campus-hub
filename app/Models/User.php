<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'nim',
        'email',
        'password',
        'jurusan',
        'semester',
        'tanggal_lahir',
        'jenis_kelamin',
        'foto',
        'poin', // Kolom poin Anda yang menyimpan total saldo poin
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    // ============================================================
    // RELASI MODEL
    // ============================================================

    // 1. Relasi ke Lapak (Sudah sesuai instruksi)
    public function lapak()     
    { 
        return $this->hasOne(Lapak::class); 
    }

    // 2. Relasi ke Order/Pesanan (Instruksi meminta 'orders', digabung agar aman)
    public function orders() 
    { 
        return $this->hasMany(Order::class); 
    }

    // Relasi pesanan lama Anda (tetap dipertahankan supaya kode lama tidak error)
    public function pesanans()  
    { 
        return $this->hasMany(Pesanan::class); 
    }

    // 3. Relasi ke Rating/Ulasan
    public function ratings()   
    { 
        return $this->hasMany(ProdukRating::class); 
    }

    // 4. Relasi Log & Penukaran Poin bawaan Anda (tetap dipertahankan)
    public function poinLogs()  
    { 
        return $this->hasMany(PoinLog::class); 
    }
    
    public function penukarans()
    { 
        return $this->hasMany(Penukaran::class); 
    }
}