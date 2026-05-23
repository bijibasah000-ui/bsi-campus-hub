<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapak_id')->constrained()->onDelete('cascade');
            $table->string('nama_produk');
            $table->enum('jenis', ['makanan','minuman','barang']);
            $table->decimal('harga', 10, 0);
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['tersedia','habis'])->default('tersedia');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('produks'); }
};
