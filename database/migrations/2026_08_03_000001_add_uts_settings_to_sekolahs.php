<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Pengaturan cetak UTS terpisah dari Rapor semester - defaultnya
            // Folio/F4 krn tabelnya lebih lebar (TP1-4 + STS sekaligus).
            $table->string('uts_ukuran_kertas')->default('F4')->after('rapor_warna_tabel');
            $table->string('uts_orientasi')->default('portrait')->after('uts_ukuran_kertas');
            $table->string('uts_font_size')->default('normal')->after('uts_orientasi');
            $table->string('uts_warna_tabel')->default('biru')->after('uts_font_size');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['uts_ukuran_kertas', 'uts_orientasi', 'uts_font_size', 'uts_warna_tabel']);
        });
    }
};
