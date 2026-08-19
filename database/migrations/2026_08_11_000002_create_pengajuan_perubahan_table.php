<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_perubahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswas')->cascadeOnDelete();
            $table->string('token', 32)->unique(); // dipakai bareng NIS+tgl lahir utk verifikasi akses publik

            $table->enum('status', ['belum_isi', 'menunggu_approval', 'sudah_approve'])->default('belum_isi');
            $table->json('data_perubahan')->nullable(); // {field: nilai_baru, ...} usulan dari siswa/wali
            $table->text('catatan_siswa')->nullable();

            $table->timestamp('diajukan_at')->nullable();
            $table->foreignId('diproses_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perubahan');
    }
};
