<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exo_instances', function (Blueprint $table) {
            // Password acak yg KITA generate & simpan sendiri (bukan password
            // asli admin) - dipakai admin_email + password ini utk update
            // user admin di Extraordinary, jadi kita tau kredensial loginnya
            // buat bantu isi otomatis, tanpa perlu tau password asli mereka.
            $table->text('admin_email_tersambung')->nullable();
            $table->text('admin_password_tersambung')->nullable();
        });

        Schema::create('exo_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('diminta_oleh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'])->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exo_requests');
        Schema::table('exo_instances', function (Blueprint $table) {
            $table->dropColumn(['admin_email_tersambung', 'admin_password_tersambung']);
        });
    }
};
