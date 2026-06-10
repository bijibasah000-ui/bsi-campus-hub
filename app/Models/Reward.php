<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = ['nama','deskripsi','poin_dibutuhkan','kategori','gambar','warna_bg','stok','aktif','syarat'];
    protected $casts = ['syarat' => 'array'];
    public function penukarans() { return $this->hasMany(Penukaran::class); }
}
