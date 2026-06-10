<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom poin ke users
        Schema::table('users', function (Blueprint $table) {
            $table->integer('poin')->default(0)->after('foto');
        });

        // Log transaksi poin
        Schema::create('poin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah');          // positif = dapat, negatif = pakai
            $table->string('keterangan');        // "Pembelian mochi cahyono", "Tukar Netflix"
            $table->string('referensi')->nullable(); // produk_id atau reward_id
            $table->string('tipe');             // 'pembelian' | 'penukaran' | 'bonus'
            $table->timestamps();
        });

        // Tabel pesanan (order) sederhana
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('produk_id')->constrained()->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('total_harga', 12, 0);
            $table->integer('poin_didapat')->default(0);
            $table->enum('status', ['pending','dikonfirmasi','selesai','dibatalkan'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
        Schema::dropIfExists('poin_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('poin');
        });
    }
};
