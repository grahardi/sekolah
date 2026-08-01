<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kokurikuler_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajarans')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->string('tema')->nullable(); // salah satu tema P5 resmi Kemendikbud
            $table->text('deskripsi_template')->nullable(); // template kalimat, siap diedit per siswa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kokurikuler_kegiatans');
    }
};
