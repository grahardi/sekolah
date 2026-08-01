<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('telepon')->nullable()->after('alamat');
            $table->string('email')->nullable()->after('telepon');
            $table->string('website')->nullable()->after('email');
            $table->string('kepala_sekolah_nama')->nullable()->after('website');
            $table->string('kepala_sekolah_nip')->nullable()->after('kepala_sekolah_nama');
            $table->string('kepala_sekolah_pangkat')->nullable()->after('kepala_sekolah_nip');
        });

        Schema::table('rapors', function (Blueprint $table) {
            $table->string('keterangan_kelulusan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'email', 'website', 'kepala_sekolah_nama', 'kepala_sekolah_nip', 'kepala_sekolah_pangkat']);
        });
        Schema::table('rapors', function (Blueprint $table) {
            $table->dropColumn('keterangan_kelulusan');
        });
    }
};
