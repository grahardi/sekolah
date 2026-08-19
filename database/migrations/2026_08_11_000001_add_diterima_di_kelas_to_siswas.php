<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Terpisah dari 'kelas' (kelas siswa SAAT INI) - krn siswa mutasi
            // bisa diterima di kelas yg beda dari kelas dia sekarang.
            $table->string('diterima_di_kelas', 20)->nullable()->after('kelas');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('diterima_di_kelas');
        });
    }
};
