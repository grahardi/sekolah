<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('logo_kabupaten')->nullable()->after('rapor_threshold_cukup'); // logo instansi/pemda, kiri
            $table->string('logo_sekolah')->nullable()->after('logo_kabupaten'); // logo sekolah, kanan
            $table->string('watermark_rapor')->nullable()->after('logo_sekolah'); // gambar watermark background
            $table->boolean('rapor_tampilkan_watermark')->default(false)->after('watermark_rapor');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['logo_kabupaten', 'logo_sekolah', 'watermark_rapor', 'rapor_tampilkan_watermark']);
        });
    }
};
