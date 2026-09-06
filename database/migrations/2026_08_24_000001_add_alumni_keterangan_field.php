<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('alumni_keterangan', 255)->nullable()->after('alumni_jurusan');
        });

        Schema::table('alumni_ajuan_ulang', function (Blueprint $table) {
            $table->string('alumni_keterangan', 255)->nullable()->after('alumni_jurusan');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('alumni_keterangan');
        });
        Schema::table('alumni_ajuan_ulang', function (Blueprint $table) {
            $table->dropColumn('alumni_keterangan');
        });
    }
};
