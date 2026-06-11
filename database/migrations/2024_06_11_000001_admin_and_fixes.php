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
        // 1. Tambah kolom baru ke tabel users
        Schema::table('users', function (Blueprint $table) {
            // role & blacklist (belum ada)
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['mahasiswa', 'admin'])->default('mahasiswa')->after('email');
            }
            if (!Schema::hasColumn('users', 'is_blacklisted')) {
                $table->boolean('is_blacklisted')->default(false)->after('role');
            }
            // name alias (admin form pakai 'name', existing pakai 'username')
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            // prodi (nim & semester sudah ada dari migration awal)
            if (!Schema::hasColumn('users', 'prodi')) {
                $table->string('prodi')->nullable()->after('jurusan');
            }
        });

        // 2. Unique constraint pada nama_toko lapak (kolom yang benar di existing table)
        // Cek dulu apakah unique constraint sudah ada
        try {
            Schema::table('lapaks', function (Blueprint $table) {
                $table->unique('nama_toko', 'lapaks_nama_toko_unique');
            });
        } catch (\Exception $e) {
            // Constraint sudah ada, lewati
        }

        // 3. Tambah kolom kategori ke lapaks jika belum ada
        if (!Schema::hasColumn('lapaks', 'kategori')) {
            Schema::table('lapaks', function (Blueprint $table) {
                $table->string('kategori')->nullable()->after('status');
            });
        }

        // 4. Tabel orders (terpisah dari pesanans yang sudah ada)
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
            if (!Schema::hasColumn('orders', 'metode_pembayaran')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('tunai')->after('total_harga');
                });
            }
        }

        // 5. Tabel ratings (order-verified, terpisah dari produk_ratings)
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->tinyInteger('bintang')->unsigned(); // 1-5
                $table->text('komentar')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'produk_id', 'order_id']);
            });
        }

        // 6. Buat akun admin default
        $adminExists = DB::table('users')->where('email', 'admin@bsicampushub.ac.id')->exists();
        if (!$adminExists) {
            DB::table('users')->insert([
                'name'          => 'Administrator',
                'username'      => 'admin',
                'email'         => 'admin@bsicampushub.ac.id',
                'password'      => Hash::make('Admin@BSI2024!'),
                'role'          => 'admin',
                'nim'           => '00000000000',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('orders');

        Schema::table('users', function (Blueprint $table) {
            $cols = ['role', 'is_blacklisted', 'name', 'prodi'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        try {
            Schema::table('lapaks', function (Blueprint $table) {
                $table->dropUnique('lapaks_nama_toko_unique');
            });
        } catch (\Exception $e) {}

        if (Schema::hasColumn('lapaks', 'kategori')) {
            Schema::table('lapaks', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }
};
