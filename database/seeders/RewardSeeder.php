<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'nama'             => 'Netflix Premium 1 Bulan',
                'deskripsi'        => 'Akses Netflix Premium selama 1 bulan. Nikmati streaming film dan series tanpa batas dengan kualitas 4K Ultra HD di 4 device sekaligus.',
                'poin_dibutuhkan'  => 15000,
                'kategori'         => 'streaming',
                'gambar'           => 'netflix',
                'warna_bg'         => '#E50914',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Akun Gmail aktif', 'Belum pernah trial Netflix', 'Proses 1x24 jam kerja']),
            ],
            [
                'nama'             => 'Netflix Basic 1 Bulan',
                'deskripsi'        => 'Akses Netflix Basic selama 1 bulan. Streaming film & series favorit di 1 device dengan kualitas HD.',
                'poin_dibutuhkan'  => 8000,
                'kategori'         => 'streaming',
                'gambar'           => 'netflix',
                'warna_bg'         => '#B20710',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Akun Gmail aktif', 'Proses 1x24 jam kerja']),
            ],
            [
                'nama'             => 'Pulsa Rp 10.000',
                'deskripsi'        => 'Pulsa senilai Rp 10.000 untuk semua operator (Telkomsel, Indosat, XL, Tri, Smartfren).',
                'poin_dibutuhkan'  => 20000,
                'kategori'         => 'pulsa',
                'gambar'           => 'pulsa',
                'warna_bg'         => '#0EA5E9',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Masukkan nomor HP saat klaim', 'Proses maks 30 menit', 'Semua operator Indonesia']),
            ],
            [
                'nama'             => 'Pulsa Rp 25.000',
                'deskripsi'        => 'Pulsa senilai Rp 25.000 untuk semua operator Indonesia.',
                'poin_dibutuhkan'  => 45000,
                'kategori'         => 'pulsa',
                'gambar'           => 'pulsa',
                'warna_bg'         => '#0369A1',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Masukkan nomor HP saat klaim', 'Proses maks 30 menit']),
            ],
            [
                'nama'             => 'Spotify Premium 1 Bulan',
                'deskripsi'        => 'Dengarkan musik tanpa iklan dengan Spotify Premium selama 1 bulan. Download lagu untuk didengarkan offline.',
                'poin_dibutuhkan'  => 10000,
                'kategori'         => 'streaming',
                'gambar'           => 'spotify',
                'warna_bg'         => '#1DB954',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Akun Spotify aktif', 'Belum pernah trial Premium', 'Proses 1x24 jam kerja']),
            ],
            [
                'nama'             => 'Voucher Makan Kantin Rp 15.000',
                'deskripsi'        => 'Voucher belanja di Pojok Jajan senilai Rp 15.000. Dapat digunakan untuk membeli produk apapun di lapak mahasiswa BSI.',
                'poin_dibutuhkan'  => 25000,
                'kategori'         => 'voucher',
                'gambar'           => 'voucher',
                'warna_bg'         => '#F59E0B',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Berlaku di seluruh lapak Pojok Jajan BSI', 'Tidak dapat digabung dengan promo lain', 'Berlaku 30 hari setelah klaim']),
            ],
            [
                'nama'             => 'Voucher Belanja Tokopedia Rp 20.000',
                'deskripsi'        => 'Voucher belanja Tokopedia senilai Rp 20.000. Minimum belanja Rp 50.000.',
                'poin_dibutuhkan'  => 35000,
                'kategori'         => 'belanja',
                'gambar'           => 'tokopedia',
                'warna_bg'         => '#03AC0E',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Minimum transaksi Rp 50.000', 'Berlaku 7 hari setelah klaim', 'Satu voucher per akun']),
            ],
            [
                'nama'             => 'Disney+ Hotstar 1 Bulan',
                'deskripsi'        => 'Akses Disney+ Hotstar selama 1 bulan. Nonton film Marvel, Star Wars, National Geographic, dan konten eksklusif.',
                'poin_dibutuhkan'  => 12000,
                'kategori'         => 'streaming',
                'gambar'           => 'disney',
                'warna_bg'         => '#113CCF',
                'stok'             => -1,
                'aktif'            => true,
                'syarat'           => json_encode(['Akun email aktif', 'Proses 1x24 jam kerja']),
            ],
        ];

        DB::table('rewards')->insert(array_map(function($r) {
            return array_merge($r, ['created_at' => now(), 'updated_at' => now()]);
        }, $rewards));
    }
}
