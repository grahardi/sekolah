<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sekalian bersihkan constraint serupa di kolom dropdown lain yg
        // rawan masalah sama (pola ini sudah berulang: status pengajuan,
        // hasil riwayat kelas, sekarang agama) - drop kalau ada, aman kalau
        // gak ada (IF EXISTS).
        $kolom = ['agama', 'pekerjaan_ayah', 'pekerjaan_ibu', 'pendidikan_ayah', 'pendidikan_ibu', 'penghasilan_ayah', 'penghasilan_ibu'];
        foreach ($kolom as $k) {
            DB::statement("ALTER TABLE siswas DROP CONSTRAINT IF EXISTS siswas_{$k}_check");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE siswas ADD CONSTRAINT siswas_agama_check CHECK (agama IN ('Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha', 'Khonghucu', 'Kepercayaan kpd Tuhan YME', 'Lainnya'))");
        // Constraint kolom lain (pekerjaan/pendidikan/penghasilan) gak
        // di-restore krn kita gak tau isi CHECK aslinya persis - kalau
        // rollback diperlukan, cek dulu struktur constraint yg dihapus.
    }
};
