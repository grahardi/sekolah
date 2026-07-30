<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (! Schema::hasColumn('siswas', 'sekolah_id')) {
                $table->foreignId('sekolah_id')->nullable()->after('id')->constrained('sekolahs')->nullOnDelete();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'aktif')) {
                $table->boolean('aktif')->default(true)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'sekolah_id')) {
                $table->dropConstrainedForeignId('sekolah_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'aktif')) {
                $table->dropColumn('aktif');
            }
        });
    }
};
