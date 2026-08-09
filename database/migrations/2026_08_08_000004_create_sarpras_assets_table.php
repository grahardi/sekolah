<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sarpras_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('kode_barang'); // nomor urut per sekolah, auto generate tapi bisa diedit
            $table->string('kode_umum')->nullable(); // misal LPX utk Laptop
            $table->string('kode_aset')->nullable(); // unik dlm satu kode_umum
            $table->string('nama_barang');
            $table->foreignId('category_id')->nullable()->constrained('sarpras_categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('sarpras_locations')->nullOnDelete();
            $table->year('tahun_pembelian')->nullable();
            $table->foreignId('funding_source_id')->nullable()->constrained('sarpras_funding_sources')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['baik', 'rusak', 'dalam_perbaikan'])->default('baik');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->unique(['sekolah_id', 'kode_barang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sarpras_assets');
    }
};
