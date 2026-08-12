<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Kalau kosong, sistem pakai key default milik sekolah.co.id
            // (dari .env). Kalau diisi, sekolah pakai key Gemini sendiri.
            $table->text('gemini_api_key')->nullable()->after('logo_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn('gemini_api_key');
        });
    }
};
