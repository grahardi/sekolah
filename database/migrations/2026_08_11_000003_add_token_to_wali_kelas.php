<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            // Token dipakai bareng No.Induk+Tgl Lahir siswa utk verifikasi akses
            // publik pengajuan perubahan data - 1 token dipakai bareng oleh
            // SELURUH siswa di kelas itu (bukan per siswa).
            $table->string('token', 32)->nullable()->after('rombel');
        });
    }

    public function down(): void
    {
        Schema::table('wali_kelas', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
