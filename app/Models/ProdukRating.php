<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProdukRating extends Model
{
    protected $fillable = ['produk_id','user_id','rating','komentar'];
    public function produk() { return $this->belongsTo(Produk::class); }
    public function user()   { return $this->belongsTo(User::class); }
}
