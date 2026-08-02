<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapors', function (Blueprint $table) {
            // Terpisah dari catatan_wali_kelas (yg dipakai di rapor akhir semester) -
            // ini khusus utk cetak UTS/PTS, gak perlu finalisasi krn UTS cuma nilai TP+STS mentah.
            $table->text('catatan_uts')->nullable()->after('catatan_wali_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('rapors', function (Blueprint $table) {
            $table->dropColumn('catatan_uts');
        });
    }
};
