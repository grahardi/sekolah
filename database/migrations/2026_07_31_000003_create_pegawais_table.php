<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();

            $table->string('nip_nuptk')->nullable(); // NIP (PNS/PPPK) atau NUPTK (non-ASN)
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            // Status kepegawaian - menentukan field ASN apa yang relevan
            $table->enum('jenis_kepegawaian', ['PNS', 'PPPK', 'GTT', 'PTT', 'GTY', 'PTY', 'Lainnya'])->default('GTT');
            $table->string('jabatan')->nullable();
            $table->string('unit_kerja')->nullable(); // Guru Mapel, TU, BK, Perpustakaan, dst

            // Khusus ASN (PNS/PPPK) - nullable karena tidak relevan utk GTT/PTT/GTY/PTY
            $table->string('golongan')->nullable();      // mis. III/a, IX
            $table->string('pangkat')->nullable();        // mis. Penata Muda
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->string('no_sk_pangkat')->nullable();

            $table->string('pendidikan_terakhir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            $table->string('foto')->nullable();

            $table->enum('status_aktif', ['Aktif', 'Cuti', 'Nonaktif', 'Pensiun', 'Pindah'])->default('Aktif');
            $table->date('tanggal_masuk')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
