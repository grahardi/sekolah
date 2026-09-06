<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_ajuan_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('alumni_kategori', 30);
            $table->foreignId('alumni_sekolah_tujuan_id')->nullable()->constrained('sekolah_tujuan')->nullOnDelete();
            $table->string('alumni_sekolah_tujuan_manual')->nullable();
            $table->string('alumni_jurusan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('diproses_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_ajuan_ulang');
    }
};
