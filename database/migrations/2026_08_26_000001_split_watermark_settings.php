<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['watermark_aktif', 'watermark_teks', 'watermark_transparansi']);

            $table->boolean('watermark_induk_aktif')->default(false);
            $table->string('watermark_induk_teks', 100)->nullable();
            $table->string('watermark_induk_gambar')->nullable();
            $table->unsignedTinyInteger('watermark_induk_transparansi')->default(10);

            $table->boolean('watermark_biodata_aktif')->default(false);
            $table->string('watermark_biodata_teks', 100)->nullable();
            $table->string('watermark_biodata_gambar')->nullable();
            $table->unsignedTinyInteger('watermark_biodata_transparansi')->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'watermark_induk_aktif', 'watermark_induk_teks', 'watermark_induk_gambar', 'watermark_induk_transparansi',
                'watermark_biodata_aktif', 'watermark_biodata_teks', 'watermark_biodata_gambar', 'watermark_biodata_transparansi',
            ]);
            $table->boolean('watermark_aktif')->default(false);
            $table->string('watermark_teks', 100)->nullable();
            $table->unsignedTinyInteger('watermark_transparansi')->default(10);
        });
    }
};
