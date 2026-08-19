<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Token sekarang per-kelas (tabel wali_kelas), bukan per-siswa lagi.
        // Pakai raw SQL (bukan ->change()) biar gak butuh doctrine/dbal.
        DB::statement('ALTER TABLE pengajuan_perubahan DROP CONSTRAINT IF EXISTS pengajuan_perubahan_token_unique');
        DB::statement('ALTER TABLE pengajuan_perubahan ALTER COLUMN token DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pengajuan_perubahan ALTER COLUMN token SET NOT NULL');
        DB::statement('ALTER TABLE pengajuan_perubahan ADD CONSTRAINT pengajuan_perubahan_token_unique UNIQUE (token)');
    }
};
