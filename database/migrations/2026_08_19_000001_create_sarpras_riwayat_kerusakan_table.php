<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sarpras_riwayat_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('sarpras_assets')->cascadeOnDelete();
            $table->date('tanggal_lapor');
            $table->text('deskripsi_kerusakan');
            $table->enum('status', ['dilaporkan', 'diperbaiki', 'tidak_bisa_diperbaiki'])->default('dilaporkan');
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('biaya_perbaikan', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sarpras_riwayat_kerusakan');
    }
};
