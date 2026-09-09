<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->boolean('is_agama')->default(false)->after('kelompok');
            $table->json('agama_untuk')->nullable()->after('is_agama');
            $table->integer('urutan')->default(0)->after('agama_untuk');
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn(['is_agama', 'agama_untuk', 'urutan']);
        });
    }
};
