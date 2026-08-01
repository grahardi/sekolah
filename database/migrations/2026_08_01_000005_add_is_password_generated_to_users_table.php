<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Penanda PERMANEN (tidak pernah direset) bahwa akun ini pernah
            // di-generate otomatis oleh sistem (Generate User/Login Sebagai/
            // Import User). Dikombinasikan dengan password_plain:
            // - is_password_generated=true & password_plain terisi -> belum ganti, WAJIB ganti
            // - is_password_generated=true & password_plain kosong -> sudah diganti
            // - is_password_generated=false -> akun biasa (bukan hasil generate), tidak relevan
            $table->boolean('is_password_generated')->default(false)->after('password_plain');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_password_generated');
        });
    }
};
