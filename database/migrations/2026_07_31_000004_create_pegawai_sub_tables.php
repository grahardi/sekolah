<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->date('tmt_pangkat_terakhir')->nullable()->after('no_sk_pangkat');
            $table->date('tmt_gaji_berkala_terakhir')->nullable()->after('tmt_pangkat_terakhir');
        });

        Schema::create('riwayat_pendidikan_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('jenjang'); // SD, SMP, SMA/SMK, D3, S1, S2, S3
            $table->string('nama_institusi');
            $table->string('jurusan')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('no_ijazah')->nullable();
            $table->timestamps();
        });

        Schema::create('keluarga_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->string('nama');
            $table->enum('hubungan', ['Suami', 'Istri', 'Anak']);
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->boolean('masih_ditanggung')->default(true); // relevan utk tunjangan
            $table->timestamps();
        });

        Schema::create('cuti_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('jenis_cuti', ['Tahunan', 'Sakit', 'Melahirkan', 'Besar', 'Alasan Penting', 'Diluar Tanggungan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->string('no_surat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('mutasi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('jenis_mutasi', ['Masuk', 'Keluar', 'Internal']);
            $table->date('tanggal_mutasi');
            $table->string('asal')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('no_sk')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_pegawais');
        Schema::dropIfExists('cuti_pegawais');
        Schema::dropIfExists('keluarga_pegawais');
        Schema::dropIfExists('riwayat_pendidikan_pegawais');

        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['tmt_pangkat_terakhir', 'tmt_gaji_berkala_terakhir']);
        });
    }
};
