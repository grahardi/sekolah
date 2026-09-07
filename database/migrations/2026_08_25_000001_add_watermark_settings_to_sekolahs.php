<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->boolean('watermark_aktif')->default(false);
            $table->string('watermark_teks', 100)->nullable();
            $table->unsignedTinyInteger('watermark_transparansi')->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['watermark_aktif', 'watermark_teks', 'watermark_transparansi']);
        });
    }
};
