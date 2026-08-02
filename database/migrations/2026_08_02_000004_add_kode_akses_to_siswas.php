<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Kode acak (bukan angka urut/id) - dipakai bareng NISN di link QR
            // rapor/UTS supaya link tidak gampang ditebak walau jumlah siswa banyak.
            $table->string('kode_akses', 20)->nullable()->after('nisn');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('kode_akses');
        });
    }
};
