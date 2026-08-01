<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Disimpan HANYA supaya admin bisa lihat/kirim ulang password ke
            // guru yang lupa - password aslinya tetap di-hash normal di kolom
            // 'password' utk proses login. Kolom ini cuma utk tampilan admin.
            $table->string('password_plain')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_plain');
        });
    }
};
