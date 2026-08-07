<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exo_instances', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // label bebas, mis. "Ujian Sekolah", "PSAJ"
            $table->string('slug')->unique(); // dipakai jg sbg nama folder: sma/exam/psaj/ujian
            $table->string('path'); // path absolut folder instalasi, mis. /home/aginza/ujian
            $table->string('port')->nullable(); // dibaca dari .env, VIEW ONLY di portal
            $table->boolean('is_aktif')->default(true);
            $table->timestamp('terakhir_dijalankan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_instances');
    }
};
