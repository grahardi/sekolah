<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: kalau tabel sudah ada (mis. dari instalasi
        // sebelumnya), migrasi ini tidak akan mencoba membuat ulang.
        if (Schema::hasTable('siswas')) {
            return;
        }

        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->unique();
            $table->string('nis', 20)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 60);
            $table->date('tanggal_lahir');
            $table->enum('agama', ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu']);
            $table->text('alamat');
            $table->string('no_telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('kelas', 10);
            $table->string('jurusan', 50)->nullable();
            $table->year('tahun_masuk');
            $table->enum('status', ['aktif','lulus','keluar','pindah'])->default('aktif');
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('no_telepon_ortu', 20)->nullable();
            $table->text('alamat_ortu')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
