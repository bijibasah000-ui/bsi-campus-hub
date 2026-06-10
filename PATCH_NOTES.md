# Patch Notes - Pojok Jajan Upgrade

## Setelah Upload ke Server, Jalankan:

```bash
# 1. Jalankan migration baru
php artisan migrate

# 2. Seed reward katalog
php artisan db:seed --class=RewardSeeder

# 3. Buat storage symlink (fix gambar tidak muncul)
php artisan storage:link

# 4. Buat folder storage yang dibutuhkan
mkdir -p storage/app/public/lapak
mkdir -p storage/app/public/produk

# 5. Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Yang Berubah:

### Bug Fix
- ✅ Gambar produk & lapak sekarang tampil dengan benar (onerror fallback emoji)
- ✅ Chip lapak sekarang bisa diklik dan filter produk per lapak
- ✅ Card produk bisa diklik dan buka modal detail

### Fitur Baru
- ⭐ Sistem Poin: dapat 300 poin per item yang dibeli
- ⭐ Rating bintang (1-5) per produk dengan komentar
- 🎁 Menu baru: Pojok Reward di sidebar & bottom nav
- 🎁 8 reward katalog (Netflix, Spotify, Pulsa, dll)
- 🎁 Tukar poin dengan kode klaim unik
- 📊 Riwayat poin & penukaran

### Database Baru
- `produk_ratings` - rating per user per produk
- `poin_logs` - log semua aktivitas poin
- `pesanans` - data pesanan produk  
- `rewards` - katalog reward
- `penukarans` - riwayat penukaran reward
- Kolom `poin` di tabel `users`
- Kolom `rating_avg`, `rating_count` di tabel `produks`
