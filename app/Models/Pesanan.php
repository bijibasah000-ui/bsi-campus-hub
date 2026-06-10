<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = ['user_id','produk_id','jumlah','total_harga','poin_didapat','status'];
    public function user()   { return $this->belongsTo(User::class); }
    public function produk() { return $this->belongsTo(Produk::class); }
}
