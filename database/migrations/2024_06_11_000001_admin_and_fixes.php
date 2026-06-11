<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom role ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['mahasiswa', 'admin'])->default('mahasiswa')->after('email');
            $table->boolean('is_blacklisted')->default(false)->after('role');
            $table->string('nim')->nullable()->after('name');
            $table->string('semester')->nullable()->after('nim');
            $table->string('prodi')->nullable()->after('semester');
        });

        // 2. Unique constraint pada nama lapak
        Schema::table('lapaks', function (Blueprint $table) {
            $table->unique('nama_lapak', 'lapaks_nama_lapak_unique');
        });

        // 3. Tabel untuk orders (jika belum ada) — dibutuhkan untuk rating setelah beli
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
                $table->integer('jumlah');
                $table->integer('total_harga');
                $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('tunai');
                $table->enum('status', ['pending', 'selesai', 'dibatalkan'])->default('pending');
                $table->timestamps();
            });
        } else {
            // Tambah kolom metode_pembayaran jika belum ada
            if (!Schema::hasColumn('orders', 'metode_pembayaran')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('tunai')->after('total_harga');
                });
            }
        }

        // 4. Tabel ratings — pisahkan dari pembelian langsung
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->tinyInteger('bintang')->unsigned(); // 1-5
                $table->text('komentar')->nullable();
                $table->timestamps();
                // Satu user hanya bisa rating satu kali per order
                $table->unique(['user_id', 'produk_id', 'order_id']);
            });
        }

        // 5. Buat akun admin default
        DB::table('users')->insert([
            'name'       => 'Administrator',
            'email'      => 'admin@bsicampushub.ac.id',
            'password'   => Hash::make('Admin@BSI2024!'),
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_blacklisted', 'nim', 'semester', 'prodi']);
        });

        Schema::table('lapaks', function (Blueprint $table) {
            $table->dropUnique('lapaks_nama_lapak_unique');
        });

        Schema::dropIfExists('ratings');
    }
};
