<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('rapor_ukuran_kertas')->default('A4')->after('kkm'); // A4, F4, Legal
            $table->string('rapor_orientasi')->default('portrait')->after('rapor_ukuran_kertas'); // portrait, landscape
            $table->string('rapor_font_size')->default('normal')->after('rapor_orientasi'); // kecil, normal, besar
            $table->date('rapor_tanggal_manual')->nullable()->after('rapor_font_size'); // override tanggal cetak, kosong = otomatis hari ini
            $table->boolean('rapor_tampilkan_logo')->default(true)->after('rapor_tanggal_manual');
            $table->string('rapor_kota_ttd')->nullable()->after('rapor_tampilkan_logo'); // kota utk baris tanda tangan, kosong = pakai kecamatan
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['rapor_ukuran_kertas', 'rapor_orientasi', 'rapor_font_size', 'rapor_tanggal_manual', 'rapor_tampilkan_logo', 'rapor_kota_ttd']);
        });
    }
};
