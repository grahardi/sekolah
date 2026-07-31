<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // guru pembuat
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis', ['DCM', 'AUM', 'Custom'])->default('Custom');
            $table->string('token', 40)->unique(); // dipakai di link publik /survey/{token}
            $table->string('target_kelas')->nullable(); // mis. "7-A,7-B,8-A" - kosong = semua kelas
            $table->enum('status', ['draft', 'aktif', 'ditutup'])->default('draft');
            $table->timestamps();
        });

        Schema::create('survey_pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->integer('urutan')->default(0);
            $table->text('teks_pertanyaan');
            $table->enum('tipe_jawaban', ['pilihan_ganda', 'checklist', 'skala', 'esai'])->default('esai');
            $table->json('opsi')->nullable(); // untuk pilihan_ganda/checklist: array pilihan
            $table->string('kategori')->nullable(); // pengelompokan bidang: pribadi/sosial/belajar/karir (khas DCM/AUM)
            $table->timestamps();
        });

        Schema::create('survey_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->json('data'); // { pertanyaan_id: jawaban, ... } - satu record per siswa per survey
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['survey_id', 'siswa_id']); // 1 siswa cuma bisa isi 1x per survey
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_jawabans');
        Schema::dropIfExists('survey_pertanyaans');
        Schema::dropIfExists('surveys');
    }
};
