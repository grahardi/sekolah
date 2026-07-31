<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama'); // mis. "2025/2026"
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();
        });

        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nama');
            $table->string('kelompok')->nullable(); // mis. "Umum", "Muatan Lokal"
            $table->timestamps();
        });

        // Wali Kelas - 1 guru jadi wali 1 kelas-rombel per tahun ajaran
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->timestamps();
        });

        // Guru Pengajar - penugasan mengajar mapel tertentu di kelas tertentu
        Schema::create('guru_pengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->timestamps();
        });

        // Guru Ekstrakurikuler
        Schema::create('guru_ekstrakurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama_ekstrakurikuler');
            $table->timestamps();
        });

        // Guru Kokurikuler (pembina projek P5 - Kurikulum Merdeka)
        Schema::create('guru_kokurikulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('tema_p5')->nullable();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_kokurikulers');
        Schema::dropIfExists('guru_ekstrakurikulers');
        Schema::dropIfExists('guru_pengajars');
        Schema::dropIfExists('wali_kelas');
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('tahun_ajarans');
    }
};
