<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            // Dari kolom "Jenis PTK" di Dapodik - Guru / Tenaga Kependidikan / Kepala Sekolah
            $table->string('kategori_pegawai')->nullable()->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn('kategori_pegawai');
        });
    }
};
