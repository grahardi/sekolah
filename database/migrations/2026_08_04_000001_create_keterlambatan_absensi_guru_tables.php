<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keterlambatan - TERPISAH dari absensi_harians (siswa gak bisa
        // absen+terlambat sekaligus di 1 hari, dicek di controller)
        Schema::create('keterlambatan_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh_guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->timestamps();
            $table->unique(['siswa_id', 'tanggal']);
        });

        // Absensi Guru - sama strukturnya dgn absensi_harians tapi utk guru
        Schema::create('absensi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['Sakit', 'Izin', 'Alpha', 'Dispensasi'])->default('Sakit');
            $table->text('keterangan')->nullable();
            $table->foreignId('dicatat_oleh_guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->timestamps();
            $table->unique(['guru_id', 'tanggal']);
        });

        // Foto bukti (Sakit/Izin) - dasar utk fitur Arsip Surat
        Schema::table('absensi_harians', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_harians', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });
        Schema::dropIfExists('absensi_gurus');
        Schema::dropIfExists('keterlambatan_siswas');
    }
};
