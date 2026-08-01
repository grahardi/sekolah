<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapor_detail_ekskuls', function (Blueprint $table) {
            $table->integer('kehadiran_hadir')->nullable()->after('nama_ekskul');
            $table->integer('kehadiran_total')->nullable()->after('kehadiran_hadir');
            $table->string('evaluasi')->nullable()->after('keterangan'); // mis. Sangat Baik/Baik/Cukup/Kurang
        });
    }

    public function down(): void
    {
        Schema::table('rapor_detail_ekskuls', function (Blueprint $table) {
            $table->dropColumn(['kehadiran_hadir', 'kehadiran_total', 'evaluasi']);
        });
    }
};
