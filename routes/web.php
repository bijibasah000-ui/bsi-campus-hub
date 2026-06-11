<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\KonselingController;
use App\Http\Controllers\PojokController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BimbinganController;

/*
|--------------------------------------------------------------------------
| Auth routes (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Protected routes (wajib login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Course
    Route::get('/course', [CourseController::class, 'index'])->name('course.index');
    Route::get('/course/{slug}', [CourseController::class, 'show'])->name('course.show');

    // Konseling (AI Chat)
    Route::get('/konseling', [KonselingController::class, 'index'])->name('konseling.index');
    Route::post('/konseling/chat', [KonselingController::class, 'chat'])->name('konseling.chat');

    // Reward
    Route::get('/pojok-reward', [RewardController::class, 'index'])->name('reward.index');
    Route::post('/pojok-reward/{reward}/tukar', [RewardController::class, 'tukar'])->name('reward.tukar');

    // Bimbingan
    Route::get('/bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');

    // Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
});

/*
|--------------------------------------------------------------------------
| Pojok Jajan — dilindungi auth + check_blacklist
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check_blacklist'])->group(function () {
    Route::get('/pojok-jajan', [PojokController::class, 'index'])->name('pojok.index');
    Route::get('/pojok-jajan/buka-lapak', [PojokController::class, 'bukaLapak'])->name('pojok.buka-lapak');
    Route::post('/pojok-jajan/buka-lapak', [PojokController::class, 'simpanLapak'])->name('pojok.simpan-lapak');
    Route::get('/pojok-jajan/tambah-produk', [PojokController::class, 'tambahProduk'])->name('pojok.tambah-produk');
    Route::post('/pojok-jajan/tambah-produk', [PojokController::class, 'simpanProduk'])->name('pojok.simpan-produk');
    Route::delete('/pojok-jajan/produk/{produk}', [PojokController::class, 'hapusProduk'])->name('pojok.hapus-produk');
    Route::get('/pojok-jajan/produk/{produk}/detail', [PojokController::class, 'detailProduk'])->name('pojok.detail-produk');

    // Rating
    Route::post('/pojok-jajan/produk/{produk}/rating', [PojokController::class, 'beriRating'])->name('pojok.beri-rating');
    Route::get('/pojok-jajan/produk/{produk}/orders-belum-rating', [PojokController::class, 'getOrdersBelumDirating'])->name('pojok.orders-belum-rating');

    // Pesan produk
    Route::post('/pojok-jajan/produk/{produk}/pesan', [PojokController::class, 'pesanProduk'])->name('pojok.pesan-produk');

    // Cek nama lapak unik
    Route::post('/pojok-jajan/check-nama-toko', [PojokController::class, 'checkNamaToko'])->name('pojok.check-nama-toko');
});

/*
|--------------------------------------------------------------------------
| Admin Panel — auth terpisah dari mahasiswa
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth admin
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Panel admin — dilindungi middleware is_admin
    Route::middleware(['auth', 'is_admin'])->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/', [AdminController::class, 'indexMahasiswa'])->name('index');
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

// Mengubah dari arrow function ke standard closure demi stabilitas di production
Route::get('/forgot-password', function () {
    return redirect()->route('login');
})->name('password.request');