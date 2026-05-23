<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'nim',
        'email',
        'password',
        'jurusan',
        'semester',
        'tanggal_lahir',
        'jenis_kelamin',
        'foto',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function lapak()
    {
        return $this->hasOne(Lapak::class);
    }
}