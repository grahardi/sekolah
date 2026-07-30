<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sekolah_id')->nullable()->after('id')->constrained('sekolahs')->nullOnDelete();
            $table->string('role')->default('admin')->after('sekolah_id'); // admin, guru, siswa
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sekolah_id');
            $table->dropColumn('role');
        });
    }
};
