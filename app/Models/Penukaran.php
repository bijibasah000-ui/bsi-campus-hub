<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Penukaran extends Model
{
    protected $fillable = ['user_id','reward_id','poin_dipakai','status','kode_klaim'];
    public function user()   { return $this->belongsTo(User::class); }
    public function reward() { return $this->belongsTo(Reward::class); }
}
