<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('status_sekolah')->nullable(); // Negeri / Swasta
            $table->string('bentuk_pendidikan')->nullable(); // SD, SMP, MTs, dst
            $table->string('jenjang_pendidikan')->nullable(); // DIKDAS, DIKMEN, PAUD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
