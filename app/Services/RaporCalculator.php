<?php

namespace App\Services;

use App\Models\PenilaianDetailNilai;

class RaporCalculator
{
    /**
     * Hitung nilai akhir (rata-rata tertimbang semua Sumatif) + deskripsi
     * capaian kompetensi otomatis (berdasarkan TP dgn skor tertinggi &
     * terendah) - persis logika dari sistem e-rapor sebelumnya, cuma
     * disesuaikan ke skema Eloquent.
     */
    public static function hitung(int $siswaId, string $kelas, ?string $rombel, int $mataPelajaranId, int $tahunAjaranId, int $semester): array
    {
        // 1. Nilai Sumatif TP (dikaitkan ke Tujuan Pembelajaran tertentu)
        $sumatifTp = PenilaianDetailNilai::query()
            ->join('penilaians', 'penilaians.id', '=', 'penilaian_detail_nilais.penilaian_id')
            ->where('penilaian_detail_nilais.siswa_id', $siswaId)
            ->where('penilaians.kelas', $kelas)
            ->where('penilaians.rombel', $rombel)
            ->where('penilaians.mata_pelajaran_id', $mataPelajaranId)
            ->where('penilaians.tahun_ajaran_id', $tahunAjaranId)
            ->where('penilaians.semester', $semester)
            ->where('penilaians.subjenis_penilaian', 'Sumatif TP')
            ->select('penilaian_detail_nilais.penilaian_id', 'penilaian_detail_nilais.nilai', 'penilaians.bobot_penilaian')
            ->get();

        // 2. Nilai Sumatif Tengah/Akhir Semester (langsung, tidak dikaitkan TP spesifik)
        $sumatifLain = PenilaianDetailNilai::query()
            ->join('penilaians', 'penilaians.id', '=', 'penilaian_detail_nilais.penilaian_id')
            ->where('penilaian_detail_nilais.siswa_id', $siswaId)
            ->where('penilaians.kelas', $kelas)
            ->where('penilaians.rombel', $rombel)
            ->where('penilaians.mata_pelajaran_id', $mataPelajaranId)
            ->where('penilaians.tahun_ajaran_id', $tahunAjaranId)
            ->where('penilaians.semester', $semester)
            ->where('penilaians.jenis_penilaian', 'Sumatif')
            ->whereIn('penilaians.subjenis_penilaian', ['Sumatif Tengah Semester', 'Sumatif Akhir Semester'])
            ->select('penilaian_detail_nilais.nilai', 'penilaians.bobot_penilaian')
            ->get();

        $totalNilaiXBobot = 0;
        $totalBobot = 0;
        $skorPerTp = []; // [deskripsi_tp => [skor, skor, ...]]

        foreach ($sumatifTp as $d) {
            $totalNilaiXBobot += $d->nilai * $d->bobot_penilaian;
            $totalBobot += $d->bobot_penilaian;

            $tps = \App\Models\Penilaian::find($d->penilaian_id)?->tujuanPembelajarans ?? collect();
            foreach ($tps as $tp) {
                $skorPerTp[$tp->deskripsi_tp][] = $d->nilai;
            }
        }

        foreach ($sumatifLain as $d) {
            $totalNilaiXBobot += $d->nilai * $d->bobot_penilaian;
            $totalBobot += $d->bobot_penilaian;
        }

        $nilaiAkhir = $totalBobot > 0 ? (int) round($totalNilaiXBobot / $totalBobot) : null;

        $deskripsi = self::buatDeskripsi($nilaiAkhir, $skorPerTp);

        return ['nilai_akhir' => $nilaiAkhir, 'deskripsi' => $deskripsi];
    }

    private static function buatDeskripsi(?int $nilaiAkhir, array $skorPerTp): string
    {
        if ($nilaiAkhir === null) {
            return '';
        }
        if (empty($skorPerTp)) {
            return 'Capaian kompetensi secara umum sudah menunjukkan ketuntasan yang baik.';
        }

        $dataMax = ['skor' => -1, 'nama' => '', 'rata' => 0];
        $dataMin = ['skor' => 101, 'nama' => '', 'rata' => 0];

        foreach ($skorPerTp as $deskripsiTp => $skorArray) {
            if (empty($skorArray)) continue;

            $maxVal = max($skorArray);
            $minVal = min($skorArray);
            $rata = array_sum($skorArray) / count($skorArray);
            $nama = lcfirst(trim($deskripsiTp));

            if ($maxVal > $dataMax['skor']) {
                $dataMax = ['skor' => $maxVal, 'nama' => $nama, 'rata' => $rata];
            }
            if ($minVal < $dataMin['skor']) {
                $dataMin = ['skor' => $minVal, 'nama' => $nama, 'rata' => $rata];
            }
        }

        if ($dataMax['nama'] === $dataMin['nama']) {
            $dataMin['nama'] = null;
        }

        $kalimatMax = $dataMax['skor'] > -1 ? self::kalimatCapaian($dataMax['rata'], $dataMax['nama']) : '';
        $kalimatMin = ($dataMin['nama'] !== null && $dataMin['skor'] < 101) ? self::kalimatCapaian($dataMin['rata'], $dataMin['nama']) : '';

        $final = trim($kalimatMax . '. ' . $kalimatMin);
        $final = ltrim($final, '. ');
        return str_replace('..', '.', $final);
    }

    private static function kalimatCapaian(float $rata, string $namaTp): string
    {
        if ($rata >= 93) return "Menunjukkan penguasaan yang sangat baik dalam {$namaTp}";
        if ($rata >= 84) return "Menunjukkan penguasaan yang baik dalam {$namaTp}";
        if ($rata >= 75) return "Menunjukkan penguasaan yang cukup dalam {$namaTp}";
        return "Perlu penguatan dalam {$namaTp}";
    }
}
