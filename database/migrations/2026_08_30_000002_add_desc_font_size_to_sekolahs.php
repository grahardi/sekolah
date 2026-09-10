<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->decimal('desc_font_size_induk', 4, 1)->default(9.5)->after('box_font_size_induk');
            $table->decimal('desc_font_size_biodata', 4, 1)->default(9.5)->after('box_font_size_biodata');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['desc_font_size_induk', 'desc_font_size_biodata']);
        });
    }
};
