<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->boolean('is_kesiswaan')->default(false)->after('is_kepsek');
        });

        Schema::create('pelanggaran_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('dilaporkan_oleh_guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->date('tanggal');
            $table->string('kategori'); // mis. "Terlambat", "Atribut Tidak Lengkap", "Merokok", dst
            $table->integer('poin')->default(0);
            $table->text('deskripsi')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->enum('status', ['Belum Ditindak', 'Sudah Ditindak'])->default('Belum Ditindak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswas');
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn('is_kesiswaan');
        });
    }
};
