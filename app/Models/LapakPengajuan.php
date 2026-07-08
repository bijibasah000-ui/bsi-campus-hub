<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapakPengajuan extends Model
{
    protected $fillable = [
        'user_id', 'nama_toko', 'deskripsi_toko', 'foto_toko', 'kontak', 'kategori',
        'durasi_bulan', 'harga', 'metode_pembayaran',
        'status', 'catatan_admin', 'disetujui_at', 'lapak_id',
    ];

    protected $casts = [
        'disetujui_at' => 'datetime',
    ];

    // Paket durasi & harga pembukaan lapak (gimik seperti iklan berbayar)
    public const PAKET_HARGA = [
        1 => 30000,
        2 => 55000,
        3 => 80000,
        4 => 100000,
        5 => 120000,
        6 => 135000,
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function lapak() { return $this->belongsTo(Lapak::class); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isDitolak()  { return $this->status === 'ditolak'; }
    public function isDisetujui(){ return $this->status === 'disetujui'; }
}
