<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PoinLog extends Model
{
    protected $fillable = ['user_id','jumlah','keterangan','referensi','tipe'];
    public function user() { return $this->belongsTo(User::class); }
}
