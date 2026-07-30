<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'rt'))              $table->string('rt', 5)->nullable()->after('alamat');
            if (!Schema::hasColumn('siswas', 'rw'))              $table->string('rw', 5)->nullable()->after('rt');
            if (!Schema::hasColumn('siswas', 'dusun'))           $table->string('dusun', 60)->nullable()->after('rw');
            if (!Schema::hasColumn('siswas', 'kelurahan'))       $table->string('kelurahan', 60)->nullable()->after('dusun');
            if (!Schema::hasColumn('siswas', 'kecamatan'))       $table->string('kecamatan', 60)->nullable()->after('kelurahan');
            if (!Schema::hasColumn('siswas', 'kode_pos'))        $table->string('kode_pos', 10)->nullable()->after('kecamatan');
            if (!Schema::hasColumn('siswas', 'nik'))             $table->string('nik', 20)->nullable()->after('email');
            if (!Schema::hasColumn('siswas', 'no_kk'))           $table->string('no_kk', 20)->nullable()->after('nik');
            if (!Schema::hasColumn('siswas', 'rombel'))          $table->string('rombel', 20)->nullable()->after('kelas');
            if (!Schema::hasColumn('siswas', 'tanggal_diterima'))$table->date('tanggal_diterima')->nullable()->after('status');
            if (!Schema::hasColumn('siswas', 'no_sttb_sd'))      $table->string('no_sttb_sd', 50)->nullable()->after('tanggal_diterima');
            if (!Schema::hasColumn('siswas', 'asal_sekolah'))    $table->string('asal_sekolah', 100)->nullable()->after('no_sttb_sd');
            if (!Schema::hasColumn('siswas', 'no_un_sd'))        $table->string('no_un_sd', 50)->nullable()->after('asal_sekolah');
            if (!Schema::hasColumn('siswas', 'anak_ke'))         $table->integer('anak_ke')->nullable()->after('no_un_sd');
            if (!Schema::hasColumn('siswas', 'lintang'))         $table->decimal('lintang', 10, 7)->nullable()->after('anak_ke');
            if (!Schema::hasColumn('siswas', 'bujur'))           $table->decimal('bujur', 10, 7)->nullable()->after('lintang');
            if (!Schema::hasColumn('siswas', 'golongan_darah'))  $table->enum('golongan_darah', ['A','B','AB','O','Tidak Tahu'])->default('Tidak Tahu')->after('bujur');
            if (!Schema::hasColumn('siswas', 'tinggi_badan'))    $table->integer('tinggi_badan')->nullable()->after('golongan_darah');
            if (!Schema::hasColumn('siswas', 'berat_badan'))     $table->integer('berat_badan')->nullable()->after('tinggi_badan');
            if (!Schema::hasColumn('siswas', 'riwayat_penyakit'))$table->string('riwayat_penyakit')->nullable()->after('berat_badan');
            if (!Schema::hasColumn('siswas', 'tahun_lahir_ayah')) $table->year('tahun_lahir_ayah')->nullable()->after('nama_ayah');
            if (!Schema::hasColumn('siswas', 'pendidikan_ayah'))  $table->string('pendidikan_ayah', 50)->nullable()->after('pekerjaan_ayah');
            if (!Schema::hasColumn('siswas', 'penghasilan_ayah')) $table->string('penghasilan_ayah', 50)->nullable()->after('pendidikan_ayah');
            if (!Schema::hasColumn('siswas', 'tahun_lahir_ibu'))  $table->year('tahun_lahir_ibu')->nullable()->after('nama_ibu');
            if (!Schema::hasColumn('siswas', 'pendidikan_ibu'))   $table->string('pendidikan_ibu', 50)->nullable()->after('pekerjaan_ibu');
            if (!Schema::hasColumn('siswas', 'penghasilan_ibu'))  $table->string('penghasilan_ibu', 50)->nullable()->after('pendidikan_ibu');
            if (!Schema::hasColumn('siswas', 'nama_wali'))        $table->string('nama_wali', 100)->nullable()->after('penghasilan_ibu');
            if (!Schema::hasColumn('siswas', 'pekerjaan_wali'))   $table->string('pekerjaan_wali', 100)->nullable()->after('nama_wali');
        });
    }

    public function down(): void
    {
        // Kolom tidak di-drop otomatis demi keamanan data
    }
};
