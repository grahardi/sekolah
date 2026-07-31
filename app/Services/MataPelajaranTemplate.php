<?php

namespace App\Services;

use App\Models\MataPelajaran;
use App\Models\Sekolah;

class MataPelajaranTemplate
{
    public static function forJenjang(string $bentukPendidikan): array
    {
        $jenjang = strtoupper(trim($bentukPendidikan));

        return match (true) {
            str_contains($jenjang, 'SD') || str_contains($jenjang, 'MI') => self::sd(),
            str_contains($jenjang, 'SMA') || str_contains($jenjang, 'MA') || str_contains($jenjang, 'SMK') => self::sma(),
            default => self::smp(), // default SMP/MTs
        };
    }

    /**
     * Buat mata pelajaran default utk 1 sekolah sesuai jenjangnya, KECUALI
     * sudah ada mapel dengan nama sama persis (cegah duplikat kalau
     * dijalankan berkali-kali). Dipakai saat registrasi baru maupun backfill
     * manual lewat `php artisan mapel:seed-default`.
     */
    public static function seedFor(Sekolah $sekolah): int
    {
        $mapelList = self::forJenjang($sekolah->bentuk_pendidikan ?: 'SMP');
        $existing = MataPelajaran::withoutGlobalScopes()
            ->where('sekolah_id', $sekolah->id)
            ->pluck('nama')
            ->map(fn ($n) => strtolower(trim($n)))
            ->toArray();

        $dibuat = 0;
        foreach ($mapelList as $m) {
            if (in_array(strtolower($m['nama']), $existing)) {
                continue;
            }
            MataPelajaran::create([
                'sekolah_id' => $sekolah->id,
                'nama' => $m['nama'],
                'kelompok' => $m['kelompok'],
            ]);
            $dibuat++;
        }

        return $dibuat;
    }

    private static function sd(): array
    {
        return [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Pancasila', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum'],
            ['nama' => 'Matematika', 'kelompok' => 'Umum'],
            ['nama' => 'Ilmu Pengetahuan Alam dan Sosial (IPAS)', 'kelompok' => 'Umum'],
            ['nama' => 'Seni Budaya', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Inggris', 'kelompok' => 'Muatan Lokal'],
        ];
    }

    private static function smp(): array
    {
        return [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Pancasila', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum'],
            ['nama' => 'Matematika', 'kelompok' => 'Umum'],
            ['nama' => 'Ilmu Pengetahuan Alam (IPA)', 'kelompok' => 'Umum'],
            ['nama' => 'Ilmu Pengetahuan Sosial (IPS)', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Inggris', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'Umum'],
            ['nama' => 'Informatika', 'kelompok' => 'Umum'],
            ['nama' => 'Seni Budaya', 'kelompok' => 'Umum'],
            ['nama' => 'Prakarya', 'kelompok' => 'Muatan Lokal'],
        ];
    }

    private static function sma(): array
    {
        return [
            ['nama' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Pancasila', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Indonesia', 'kelompok' => 'Umum'],
            ['nama' => 'Matematika', 'kelompok' => 'Umum'],
            ['nama' => 'Bahasa Inggris', 'kelompok' => 'Umum'],
            ['nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'Umum'],
            ['nama' => 'Sejarah', 'kelompok' => 'Umum'],
            ['nama' => 'Seni Budaya', 'kelompok' => 'Umum'],
            ['nama' => 'Informatika', 'kelompok' => 'Umum'],
            ['nama' => 'Fisika', 'kelompok' => 'Peminatan'],
            ['nama' => 'Kimia', 'kelompok' => 'Peminatan'],
            ['nama' => 'Biologi', 'kelompok' => 'Peminatan'],
            ['nama' => 'Sosiologi', 'kelompok' => 'Peminatan'],
            ['nama' => 'Ekonomi', 'kelompok' => 'Peminatan'],
            ['nama' => 'Geografi', 'kelompok' => 'Peminatan'],
        ];
    }
}
