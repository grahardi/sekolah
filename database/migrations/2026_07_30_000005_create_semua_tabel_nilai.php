<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('nilai_rapors')) {
            Schema::create('nilai_rapors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('tahun_ajaran', 10);
                $table->tinyInteger('semester');
                $table->string('kelas', 10);
                $table->string('mata_pelajaran', 80);
                $table->string('kelompok', 30)->default('Umum');
                $table->decimal('nilai', 5, 2)->nullable();
                $table->text('deskripsi')->nullable();
                $table->timestamps();
                $table->unique(['siswa_id','tahun_ajaran','semester','mata_pelajaran'], 'uk_nilai_rapor');
                $table->index(['siswa_id','tahun_ajaran','semester']);
            });
        }

        if (!Schema::hasTable('nilai_p5s')) {
            Schema::create('nilai_p5s', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('tahun_ajaran', 10);
                $table->tinyInteger('semester');
                $table->string('kelas', 10);
                $table->string('tema_projek', 100);
                $table->string('topik', 100)->nullable();
                $table->enum('nilai', ['BB','MB','BSH','BSB']);
                $table->text('deskripsi')->nullable();
                $table->timestamps();
                $table->index(['siswa_id','tahun_ajaran','semester']);
            });
        }

        if (!Schema::hasTable('nilai_ekskuls')) {
            Schema::create('nilai_ekskuls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('tahun_ajaran', 10);
                $table->tinyInteger('semester');
                $table->string('nama_ekskul', 80);
                $table->string('nilai_kualitatif', 30)->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
                $table->index(['siswa_id','tahun_ajaran','semester']);
            });
        }

        if (!Schema::hasTable('kehadirans')) {
            Schema::create('kehadirans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('tahun_ajaran', 10);
                $table->tinyInteger('semester');
                $table->string('kelas', 10);
                $table->integer('sakit')->default(0);
                $table->integer('izin')->default(0);
                $table->integer('alpa')->default(0);
                $table->timestamps();
                $table->unique(['siswa_id','tahun_ajaran','semester'], 'uk_kehadiran');
            });
        }

        if (!Schema::hasTable('riwayat_kelas')) {
            Schema::create('riwayat_kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('tahun_ajaran', 10);
                $table->string('kelas', 10);
                $table->string('rombel', 20)->nullable();
                $table->string('wali_kelas', 100)->nullable();
                $table->enum('hasil', ['Naik Kelas','Tinggal Kelas','Lulus','Pindah','Keluar'])->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
                $table->unique(['siswa_id','tahun_ajaran'], 'uk_riwayat_kelas');
            });
        }

        if (!Schema::hasTable('arsip_berkas')) {
            Schema::create('arsip_berkas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->string('foto')->nullable();
                $table->string('kartu_keluarga')->nullable();
                $table->string('akta_lahir')->nullable();
                $table->string('ijazah_sd')->nullable();
                $table->string('ijazah')->nullable();
                $table->string('transkrip_nilai')->nullable();
                $table->string('sertifikat_tka')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('prestasis')) {
            Schema::create('prestasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
                $table->date('tanggal_kegiatan');
                $table->string('jenis_lomba', 100);
                $table->enum('tingkat_lomba', ['Sekolah','Kecamatan','Kabupaten/Kota','Provinsi','Nasional','Internasional']);
                $table->string('juara', 30);
                $table->string('penyelenggara', 100)->nullable();
                $table->text('keterangan')->nullable();
                $table->string('sertifikat')->nullable();
                $table->timestamps();
                $table->index(['siswa_id','tanggal_kegiatan']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasis');
        Schema::dropIfExists('arsip_berkas');
        Schema::dropIfExists('riwayat_kelas');
        Schema::dropIfExists('kehadirans');
        Schema::dropIfExists('nilai_ekskuls');
        Schema::dropIfExists('nilai_p5s');
        Schema::dropIfExists('nilai_rapors');
    }
};
