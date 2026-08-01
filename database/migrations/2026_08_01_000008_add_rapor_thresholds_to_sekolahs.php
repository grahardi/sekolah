<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Ambang batas deskripsi capaian kompetensi rapor - persis nilai
            // dari algoritma asli (93/84/75). Kolom baru dgn default otomatis
            // berlaku juga utk sekolah yg sudah terdaftar sebelumnya.
            $table->integer('rapor_threshold_sangat_baik')->default(93)->after('rapor_kota_ttd');
            $table->integer('rapor_threshold_baik')->default(84)->after('rapor_threshold_sangat_baik');
            $table->integer('rapor_threshold_cukup')->default(75)->after('rapor_threshold_baik');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['rapor_threshold_sangat_baik', 'rapor_threshold_baik', 'rapor_threshold_cukup']);
        });
    }
};
