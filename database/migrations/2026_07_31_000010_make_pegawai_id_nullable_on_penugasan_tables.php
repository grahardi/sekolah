<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pakai raw SQL (bukan Schema::change()) supaya tidak perlu install
        // doctrine/dbal cuma buat 1 migrasi kecil ini.
        foreach (['wali_kelas', 'guru_pengajars', 'guru_ekstrakurikulers', 'guru_kokurikulers'] as $t) {
            DB::statement("ALTER TABLE {$t} ALTER COLUMN pegawai_id DROP NOT NULL");
        }
    }

    public function down(): void
    {
        foreach (['wali_kelas', 'guru_pengajars', 'guru_ekstrakurikulers', 'guru_kokurikulers'] as $t) {
            DB::statement("ALTER TABLE {$t} ALTER COLUMN pegawai_id SET NOT NULL");
        }
    }
};
