<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['lapak_id','nama_produk','jenis','harga','deskripsi','foto','status','rating_avg','rating_count'];
    public function lapak()   { return $this->belongsTo(Lapak::class); }
    public function ratings() { return $this->hasMany(ProdukRating::class); }
    public function pesanans(){ return $this->hasMany(Pesanan::class); }
}
