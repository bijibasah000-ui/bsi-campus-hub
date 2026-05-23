<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['lapak_id','nama_produk','jenis','harga','deskripsi','foto','status'];
    public function lapak() { return $this->belongsTo(Lapak::class); }
}
