<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Katalog reward
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('poin_dibutuhkan');
            $table->string('kategori');         // 'streaming','pulsa','voucher','belanja'
            $table->string('gambar')->nullable();// nama file gambar
            $table->string('warna_bg')->default('#6366f1'); // warna card
            $table->integer('stok')->default(-1); // -1 = unlimited
            $table->boolean('aktif')->default(true);
            $table->json('syarat')->nullable();  // array persyaratan
            $table->timestamps();
        });

        // Log penukaran reward
        Schema::create('penukarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->integer('poin_dipakai');
            $table->enum('status', ['pending','diproses','selesai','ditolak'])->default('pending');
            $table->string('kode_klaim')->nullable(); // kode unik untuk klaim
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('penukarans');
        Schema::dropIfExists('rewards');
    }
};
