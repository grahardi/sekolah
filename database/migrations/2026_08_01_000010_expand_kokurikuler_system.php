<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kokurikuler_kegiatans', function (Blueprint $table) {
            $table->string('bentuk_kegiatan')->nullable()->after('tema'); // Lintas Disiplin / G7KAIH / Khas Sekolah
            $table->foreignId('koordinator_guru_id')->nullable()->after('bentuk_kegiatan')->constrained('gurus')->nullOnDelete();
            $table->integer('semester')->nullable()->after('koordinator_guru_id');
        });

        Schema::create('kokurikuler_target_dimensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kokurikuler_kegiatans')->cascadeOnDelete();
            $table->string('nama_dimensi');
            $table->timestamps();
        });

        Schema::create('kokurikuler_kelas_terlibats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kokurikuler_kegiatans')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->timestamps();
        });

        Schema::create('kokurikuler_mapel_terlibats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kokurikuler_kegiatans')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('kokurikuler_asesmens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_dimensi_id')->constrained('kokurikuler_target_dimensis')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('nilai_kualitatif', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
            $table->unique(['target_dimensi_id', 'siswa_id'], 'uk_asesmen_dimensi_siswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kokurikuler_asesmens');
        Schema::dropIfExists('kokurikuler_mapel_terlibats');
        Schema::dropIfExists('kokurikuler_kelas_terlibats');
        Schema::dropIfExists('kokurikuler_target_dimensis');
        Schema::table('kokurikuler_kegiatans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('koordinator_guru_id');
            $table->dropColumn(['bentuk_kegiatan', 'semester']);
        });
    }
};
