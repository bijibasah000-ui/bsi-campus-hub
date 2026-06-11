<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lapak extends Model
{
    protected $fillable = [
        'user_id', 'nama_toko', 'deskripsi_toko',
        'foto_toko', 'kontak', 'status', 'kategori',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function produks() { return $this->hasMany(Produk::class); }
}
