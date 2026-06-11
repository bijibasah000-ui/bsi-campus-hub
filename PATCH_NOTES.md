# BSI Campus Hub — Patch Notes

## Fix Deploy v1.1 — 2026-06-11

### Bug Fixes (Railway / Production)

#### [CRITICAL] Dockerfile — Startup crash saat DB belum siap
- **Problem:** Startup script langsung menjalankan `php artisan migrate --force` dengan `set -e` aktif. Jika MySQL Railway belum siap, container exit → 502 Bad Gateway.
- **Fix:** Ditambahkan retry loop (max 30 percobaan × 2 detik = 60 detik) yang menunggu DB benar-benar bisa diakses sebelum menjalankan migrate.
- **File:** `Dockerfile`

#### [CRITICAL] Dockerfile — Storage symlink tidak dibuat
- **Problem:** Foto profil, lapak, dan produk tidak bisa tampil karena `public/storage` symlink tidak pernah dibuat saat deployment.
- **Fix:** Ditambahkan `php artisan storage:link --force` di startup script.
- **File:** `Dockerfile`

#### [WARNING] AppServiceProvider — HTTPS tidak di-force untuk Railway
- **Problem:** Railway adalah HTTPS reverse proxy. Tanpa `URL::forceScheme('https')`, asset URL bisa ter-generate sebagai `http://` → mixed content warning / asset tidak load.
- **Fix:** Ditambahkan `URL::forceScheme('https')` di method `boot()` saat `APP_ENV=production`.
- **File:** `app/Providers/AppServiceProvider.php`

#### [WARNING] KonselingController — SSL verification dimatikan
- **Problem:** `Http::withoutVerifying()` digunakan untuk koneksi ke Groq API, yang tidak aman di production.
- **Fix:** Diganti dengan `Http::timeout(30)`. PHP 8.3 CLI Docker image sudah memiliki CA certificates lengkap.
- **File:** `app/Http/Controllers/KonselingController.php`

#### [INFO] routes/web.php — Route `/bimbingan` tidak terdaftar
- **Problem:** `BimbinganController` dan view `bimbingan.index` sudah ada tapi tidak ada route-nya, sehingga halaman tidak bisa diakses.
- **Fix:** Ditambahkan `Route::get('/bimbingan', ...)` di dalam grup middleware `auth`.
- **File:** `routes/web.php`

---

## Release v1.0 — Initial Release
- Auth mahasiswa (register/login/logout)
- Dashboard & event kampus
- Pojok Jajan (lapak, produk, order, rating)
- Pojok Reward (tukar poin)
- Konseling AI dengan Kak Sari (Groq/LLaMA)
- Course & Bimbingan
- Admin panel (mahasiswa, lapak, produk)
