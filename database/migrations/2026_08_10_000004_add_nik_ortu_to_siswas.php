<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nik_ayah', 20)->nullable()->after('nama_ayah');
            $table->string('nik_ibu', 20)->nullable()->after('nama_ibu');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nik_ayah', 'nik_ibu']);
        });
    }
};
