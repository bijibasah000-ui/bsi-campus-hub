<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapak_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Data lapak yang diajukan (sama seperti field di tabel lapaks)
            $table->string('nama_toko');
            $table->text('deskripsi_toko')->nullable();
            $table->string('foto_toko')->nullable();
            $table->string('kontak')->nullable();
            $table->string('kategori')->nullable();

            // Paket sewa/pembukaan lapak (gimik seperti iklan berbayar)
            $table->unsignedTinyInteger('durasi_bulan');      // 1 - 6
            $table->unsignedInteger('harga');                 // biaya sesuai durasi
            $table->enum('metode_pembayaran', ['qris', 'tunai'])->default('qris');

            // Status pengajuan
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();        // alasan tolak (opsional)
            $table->timestamp('disetujui_at')->nullable();

            // Jika disetujui, tersambung ke lapak yang benar-benar dibuat
            $table->foreignId('lapak_id')->nullable()->constrained('lapaks')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapak_pengajuans');
    }
};
