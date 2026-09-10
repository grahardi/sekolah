<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('box_fill_induk', 10)->default('#000000')->after('watermark_induk_transparansi');
            $table->string('box_font_induk', 10)->default('#ffffff')->after('box_fill_induk');
            $table->string('box_fill_biodata', 10)->default('#000000')->after('watermark_biodata_transparansi');
            $table->string('box_font_biodata', 10)->default('#ffffff')->after('box_fill_biodata');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['box_fill_induk', 'box_font_induk', 'box_fill_biodata', 'box_font_biodata']);
        });
    }
};
