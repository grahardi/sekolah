<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('alumni_kategori', 30)->nullable();
            $table->foreignId('alumni_sekolah_tujuan_id')->nullable()->constrained('sekolah_tujuan')->nullOnDelete();
            $table->string('alumni_sekolah_tujuan_manual')->nullable();
            $table->string('alumni_jurusan')->nullable();
            $table->timestamp('alumni_diisi_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('alumni_sekolah_tujuan_id');
            $table->dropColumn(['alumni_kategori', 'alumni_sekolah_tujuan_manual', 'alumni_jurusan', 'alumni_diisi_at']);
        });
    }
};
