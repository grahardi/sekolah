<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            // Nullable - kalau guru ini juga pegawai resmi di Kepegawaian.
            // Kalau null, berarti "guru bantu" yg cuma dikenal di E-Rapor saja.
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->string('nama');
            $table->string('nip_nuptk')->nullable();
            $table->string('keterangan')->nullable(); // mis. "Guru Bantu", "Guru Tamu"
            $table->timestamps();
        });

        foreach (['wali_kelas', 'guru_pengajars', 'guru_ekstrakurikulers', 'guru_kokurikulers'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                $table->foreignId('guru_id')->nullable()->after('pegawai_id')->constrained('gurus')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['wali_kelas', 'guru_pengajars', 'guru_ekstrakurikulers', 'guru_kokurikulers'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropConstrainedForeignId('guru_id');
            });
        }
        Schema::dropIfExists('gurus');
    }
};
