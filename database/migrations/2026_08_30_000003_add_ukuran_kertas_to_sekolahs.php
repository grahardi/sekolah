<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('induk_ukuran_kertas', 5)->default('A4')->after('desc_font_size_induk');
            $table->string('biodata_ukuran_kertas', 5)->default('F4')->after('desc_font_size_biodata');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['induk_ukuran_kertas', 'biodata_ukuran_kertas']);
        });
    }
};
