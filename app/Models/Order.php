<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produk_id',
        'jumlah',
        'total_harga',
        'metode_pembayaran',
        'status',
    ];

    public function user()   { return $this->belongsTo(User::class); }
    public function produk() { return $this->belongsTo(Produk::class); }
    public function rating() { return $this->hasOne(Rating::class); }
}
