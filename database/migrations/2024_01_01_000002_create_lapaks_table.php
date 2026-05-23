<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lapaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_toko');
            $table->text('deskripsi_toko')->nullable();
            $table->string('foto_toko')->nullable();
            $table->string('kontak')->nullable();
            $table->enum('status', ['aktif','nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('lapaks'); }
};
