<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('pegawai_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            // role sudah enum-like string bebas (admin/induk) - pastikan
            // kolomnya cukup panjang utk nilai 'guru' juga (sudah string, aman)
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
