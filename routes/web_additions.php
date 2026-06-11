<?php
// routes/web.php — tambahkan routes berikut

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PojokJajanController;

// ============================================================
// ADMIN ROUTES (tidak pakai auth middleware biasa)
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth admin (halaman terpisah dari login mahasiswa)
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Panel admin — dilindungi middleware IsAdmin
    Route::middleware(['auth', 'is_admin'])->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/', [AdminController::class, 'indexMahasiswa'])->name('index');
            Route::get('/{user}/edit', [AdminController::class, 'editMahasiswa'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateMahasiswa'])->name('update');
            Route::post('/{user}/blacklist', [AdminController::class, 'toggleBlacklist'])->name('blacklist');
            Route::post('/{user}/poin', [AdminController::class, 'updatePoin'])->name('poin');
        });

        // Lapak
        Route::prefix('lapak')->name('lapak.')->group(function () {
            Route::get('/', [AdminController::class, 'indexLapak'])->name('index');
            Route::delete('/{lapak}', [AdminController::class, 'deleteLapak'])->name('delete');
            Route::post('/hapus-duplikat', [AdminController::class, 'hapusDuplikatLapak'])->name('hapus-duplikat');
        });

        // Produk
        Route::prefix('produk')->name('produk.')->group(function () {
            Route::get('/', [AdminController::class, 'indexProduk'])->name('index');
            Route::delete('/{produk}', [AdminController::class, 'deleteProduk'])->name('delete');
        });
    });
});

// ============================================================
// POJOK JAJAN ROUTES (auth mahasiswa biasa)
// ============================================================
Route::middleware(['auth', 'check_blacklist'])->prefix('pojok-jajan')->name('pojok-jajan.')->group(function () {

    // Cek nama lapak (AJAX untuk real-time feedback)
    Route::post('/check-nama-lapak', [PojokJajanController::class, 'checkNamaLapak'])->name('check-nama');

    // Buat lapak baru
    Route::post('/lapak', [PojokJajanController::class, 'storeLapak'])->name('lapak.store');

    // Pesan produk — sudah ada block penjual beli sendiri
    Route::post('/produk/{produk}/pesan', [PojokJajanController::class, 'pesanProduk'])->name('pesan');

    // Rating — hanya setelah beli
    Route::get('/produk/{produk}/orders-belum-rating', [PojokJajanController::class, 'getOrdersBelumDirating'])->name('orders-belum-rating');
    Route::post('/produk/{produk}/rating', [PojokJajanController::class, 'submitRating'])->name('rating');
});
