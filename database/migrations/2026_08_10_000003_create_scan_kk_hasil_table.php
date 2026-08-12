<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_kk_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswas')->cascadeOnDelete();

            $table->enum('status_kk', ['belum', 'ok', 'error'])->default('belum');
            $table->integer('skor_kk')->nullable(); // 0-100
            $table->json('data_kk')->nullable(); // hasil OCR mentah dari Gemini

            $table->enum('status_akta', ['belum', 'ok', 'error'])->default('belum');
            $table->integer('skor_akta')->nullable();
            $table->string('no_akta')->nullable();
            $table->json('data_akta')->nullable();

            $table->text('pesan_error')->nullable();
            $table->timestamp('discan_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_kk_hasil');
    }
};
