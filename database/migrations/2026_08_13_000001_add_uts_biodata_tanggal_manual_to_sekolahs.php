<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Terpisah dari rapor_tanggal_manual - sebelumnya UTS malah gak
            // pakai setting apapun (hardcode now()), Biodata numpang ke
            // punya Rapor. Sekarang masing2 py setting sendiri.
            $table->date('uts_tanggal_manual')->nullable()->after('rapor_tanggal_manual');
            $table->date('biodata_tanggal_manual')->nullable()->after('uts_tanggal_manual');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['uts_tanggal_manual', 'biodata_tanggal_manual']);
        });
    }
};
