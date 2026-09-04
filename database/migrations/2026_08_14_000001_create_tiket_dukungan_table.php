<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_dukungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('dibuat_oleh_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subjek');
            $table->enum('status', ['terbuka', 'diproses', 'selesai'])->default('terbuka');
            $table->enum('prioritas', ['rendah', 'normal', 'tinggi'])->default('normal');
            $table->timestamp('dibalas_terakhir_at')->nullable(); // buat urutkan "paling baru dibalas"
            $table->boolean('ada_balasan_belum_dibaca_sekolah')->default(false);
            $table->boolean('ada_balasan_belum_dibaca_admin')->default(true); // tiket baru = belum dibaca admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_dukungan');
    }
};
