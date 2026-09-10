<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->unsignedTinyInteger('box_font_size_induk')->default(9)->after('box_font_induk');
            $table->unsignedTinyInteger('box_font_size_biodata')->default(9)->after('box_font_biodata');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['box_font_size_induk', 'box_font_size_biodata']);
        });
    }
};
