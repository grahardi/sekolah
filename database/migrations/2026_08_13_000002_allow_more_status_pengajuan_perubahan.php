<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel bikin enum() di Postgres pakai CHECK CONSTRAINT (bukan
        // native enum type) - drop constraint lama, ganti kolom jadi varchar
        // biasa (validasi cukup di level aplikasi, lebih fleksibel drpd
        // harus migration lagi tiap nambah status baru).
        DB::statement('ALTER TABLE pengajuan_perubahan DROP CONSTRAINT IF EXISTS pengajuan_perubahan_status_check');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengajuan_perubahan ADD CONSTRAINT pengajuan_perubahan_status_check CHECK (status IN ('belum_isi', 'menunggu_approval', 'sudah_approve'))");
    }
};
