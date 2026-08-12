<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip_berkas', function (Blueprint $table) {
            // Array JSON [{path, nama_asli}, ...] - utk berkas yg gak masuk
            // kategori spesifik (bukan foto/KK/akta/ijazah/dst), bisa lebih dari 1.
            $table->json('berkas_lain')->nullable()->after('sertifikat_tka');
        });
    }

    public function down(): void
    {
        Schema::table('arsip_berkas', function (Blueprint $table) {
            $table->dropColumn('berkas_lain');
        });
    }
};
