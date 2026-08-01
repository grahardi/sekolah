<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KKM (Kriteria Ketuntasan Minimal) - disimpan per sekolah, dipakai
        // sbg acuan tampilan warna nilai di leger/rapor.
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->integer('kkm')->default(75)->after('bentuk_pendidikan');
        });

        // Tujuan Pembelajaran (TP) - unit terkecil kompetensi Kurikulum Merdeka
        // per mapel. Nilai sumatif TP dikaitkan ke TP tertentu, dipakai utk
        // hitung deskripsi capaian kompetensi otomatis di rapor.
        Schema::create('tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('fase', 5)->nullable(); // mis. "D" utk SMP
            $table->string('kode_tp', 20)->nullable();
            $table->text('deskripsi_tp');
            $table->integer('semester'); // 1 = Ganjil, 2 = Genap
            $table->timestamps();
        });

        // TP berlaku utk kelas-rombel mana saja (1 TP bisa dipakai lebih dari 1 kelas paralel)
        Schema::create('tp_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajarans')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->timestamps();
            $table->unique(['tujuan_pembelajaran_id', 'kelas', 'rombel'], 'uk_tp_kelas');
        });

        // Penilaian = 1 sesi ujian/tugas/asesmen yang dibuat guru
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->string('nama_penilaian');
            $table->enum('jenis_penilaian', ['Formatif', 'Sumatif']);
            $table->enum('subjenis_penilaian', ['Sumatif TP', 'Sumatif Tengah Semester', 'Sumatif Akhir Semester', 'Sumatif Akhir Tahun'])->nullable();
            $table->integer('bobot_penilaian')->default(1);
            $table->integer('semester');
            $table->date('tanggal_penilaian')->nullable();
            $table->timestamps();
        });

        // 1 penilaian (khususnya Sumatif TP) bisa menguji lebih dari 1 TP sekaligus
        Schema::create('penilaian_tp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaians')->cascadeOnDelete();
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajarans')->cascadeOnDelete();
            $table->timestamps();
        });

        // Nilai murid per penilaian (0-100)
        Schema::create('penilaian_detail_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaians')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamps();
            $table->unique(['penilaian_id', 'siswa_id'], 'uk_penilaian_siswa');
        });

        // Rapor - 1 record per siswa per tahun ajaran per semester
        Schema::create('rapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('rombel')->nullable();
            $table->integer('semester');
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('tanpa_keterangan')->default(0);
            $table->text('catatan_wali_kelas')->nullable();
            $table->text('deskripsi_kokurikuler')->nullable();
            $table->text('deskripsi_ekstrakurikuler')->nullable();
            $table->enum('status', ['Draft', 'Final'])->default('Draft');
            $table->date('tanggal_rapor')->nullable();
            $table->timestamps();
            $table->unique(['siswa_id', 'tahun_ajaran_id', 'semester'], 'uk_rapor_siswa');
        });

        // Nilai akhir + deskripsi capaian kompetensi per mapel (hasil hitung otomatis)
        Schema::create('rapor_detail_akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapors')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->integer('nilai_akhir')->nullable(); // hasil hitung sistem
            $table->integer('nilai_katrol')->nullable(); // koreksi manual guru (opsional)
            $table->text('capaian_kompetensi')->nullable(); // deskripsi auto-generate
            $table->timestamps();
            $table->unique(['rapor_id', 'mata_pelajaran_id'], 'uk_rapor_mapel');
        });

        Schema::create('rapor_detail_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapors')->cascadeOnDelete();
            $table->string('nama_ekskul');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_detail_ekskuls');
        Schema::dropIfExists('rapor_detail_akademiks');
        Schema::dropIfExists('rapors');
        Schema::dropIfExists('penilaian_detail_nilais');
        Schema::dropIfExists('penilaian_tp');
        Schema::dropIfExists('penilaians');
        Schema::dropIfExists('tp_kelas');
        Schema::dropIfExists('tujuan_pembelajarans');

        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('kkm');
        });
    }
};
