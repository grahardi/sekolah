<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exo_instances', function (Blueprint $table) {
            $table->foreignId('sekolah_id')->nullable()->after('nama')->constrained('sekolahs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exo_instances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sekolah_id');
        });
    }
};
