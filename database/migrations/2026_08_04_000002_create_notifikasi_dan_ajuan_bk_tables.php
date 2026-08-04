<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notif Wali Kelas - catatan ringan pembinaan, TIDAK masuk poin
        // pelanggaran formal, cuma info ke wali kelas.
        Schema::create('notifikasi_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('dari_guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->text('pesan');
            $table->boolean('sudah_dibaca')->default(false);
            $table->timestamps();
        });

        // Ajuan BK - referral ringan ke BK, integrasi penuh dgn Program BK
        // menyusul, utk sekarang cuma catat pengajuannya.
        Schema::create('ajuan_bk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('diajukan_oleh_guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->text('alasan')->nullable();
            $table->enum('status', ['Menunggu', 'Ditindak'])->default('Menunggu');
            $table->timestamps();
        });

        // Sederhanakan Pelanggaran: kategori sekarang diisi tingkat keparahan
        // (Peringatan/Ringan/Sedang/Berat) sesuai referensi, bukan lagi jenis
        // spesifik (Terlambat/Merokok/dll) - itu masuk ke deskripsi bebas.
    }

    public function down(): void
    {
        Schema::dropIfExists('ajuan_bk');
        Schema::dropIfExists('notifikasi_wali_kelas');
    }
};
