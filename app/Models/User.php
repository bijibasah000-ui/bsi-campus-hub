<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username','nim','email','password','jurusan','semester',
        'tanggal_lahir','jenis_kelamin','foto','poin',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function lapak()     { return $this->hasOne(Lapak::class); }
    public function ratings()   { return $this->hasMany(ProdukRating::class); }
    public function pesanans()  { return $this->hasMany(Pesanan::class); }
    public function poinLogs()  { return $this->hasMany(PoinLog::class); }
    public function penukarans(){ return $this->hasMany(Penukaran::class); }
}
