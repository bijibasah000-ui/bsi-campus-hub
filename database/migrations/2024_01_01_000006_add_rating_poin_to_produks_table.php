<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->default(0)->after('status');
            $table->integer('rating_count')->default(0)->after('rating_avg');
        });

        Schema::create('produk_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating'); // 1-5
            $table->text('komentar')->nullable();
            $table->timestamps();
            $table->unique(['produk_id','user_id']); // 1 rating per user per produk
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('produk_ratings');
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['rating_avg','rating_count']);
        });
    }
};
