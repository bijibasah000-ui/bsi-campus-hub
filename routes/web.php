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

/*
|--------------------------------------------------------------------------
| Auth routes (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
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
    Route::get('/profile',  [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');

    // Course
    Route::get('/course',         [CourseController::class, 'index'])->name('course.index');
    Route::get('/course/{slug}',  [CourseController::class, 'show'])->name('course.show');

    // Konseling (AI Chat)
    Route::get('/konseling',       [KonselingController::class, 'index'])->name('konseling.index');
    Route::post('/konseling/chat', [KonselingController::class, 'chat'])->name('konseling.chat');

    // Pojok Jajan
    Route::get('/pojok-jajan',                          [PojokController::class, 'index'])->name('pojok.index');
    Route::get('/pojok-jajan/buka-lapak',               [PojokController::class, 'bukaLapak'])->name('pojok.buka-lapak');
    Route::post('/pojok-jajan/buka-lapak',              [PojokController::class, 'simpanLapak'])->name('pojok.simpan-lapak');
    Route::get('/pojok-jajan/tambah-produk',            [PojokController::class, 'tambahProduk'])->name('pojok.tambah-produk');
    Route::post('/pojok-jajan/tambah-produk',           [PojokController::class, 'simpanProduk'])->name('pojok.simpan-produk');
    Route::delete('/pojok-jajan/produk/{produk}',       [PojokController::class, 'hapusProduk'])->name('pojok.hapus-produk');
    Route::get('/pojok-jajan/produk/{produk}/detail',   [PojokController::class, 'detailProduk'])->name('pojok.detail-produk');
    Route::post('/pojok-jajan/produk/{produk}/rating',  [PojokController::class, 'beriRating'])->name('pojok.beri-rating');
    Route::post('/pojok-jajan/produk/{produk}/pesan',   [PojokController::class, 'pesanProduk'])->name('pojok.pesan-produk');

    // Pojok Reward
    Route::get('/pojok-reward',                         [RewardController::class, 'index'])->name('reward.index');
    Route::post('/pojok-reward/{reward}/tukar',         [RewardController::class, 'tukar'])->name('reward.tukar');

    // Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
});

Route::get('/forgot-password', fn() => redirect()->route('login'))->name('password.request');
