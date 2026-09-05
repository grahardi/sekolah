<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('sarpras_prefix_kode', 20)->nullable();
            $table->integer('sarpras_ambang_batas_pinjam_hari')->default(7);
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['sarpras_prefix_kode', 'sarpras_ambang_batas_pinjam_hari']);
        });
    }
};
