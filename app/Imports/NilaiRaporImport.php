<?php

namespace App\Imports;

use App\Models\{Siswa, NilaiRapor};
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;

class NilaiRaporImport
{
    protected array $errors   = [];
    protected array $warnings = [];
    protected int   $imported = 0;
    protected int   $skipped  = 0;

    const FIXED_COLS = ['nisn','nis','nama_lengkap','nama','kelas','rombel'];

    public function import(string $filePath, string $tahunAjaran, int $semester): void
    {
        if (!file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan.";
            return;
        }

        try {
            $rows = (new FastExcel)->import($filePath);
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membaca file: ' . $e->getMessage();
            return;
        }

        if ($rows->isEmpty()) {
            $this->errors[] = 'File kosong atau format tidak dikenali.';
            return;
        }

        $headers   = array_keys($rows->first());
        $mapelCols = array_values(array_filter(
            $headers,
            fn($h) => !in_array(strtolower(trim($h)), self::FIXED_COLS)
        ));

        if (empty($mapelCols)) {
            $this->errors[] = 'Tidak ada kolom mata pelajaran ditemukan. Pastikan header file sesuai template.';
            return;
        }

        foreach ($rows as $rowNum => $row) {
            $this->processRow($row, $rowNum + 2, $tahunAjaran, $semester, $mapelCols);
        }
    }

    private function processRow(array $row, int $lineNum, string $ta, int $sem, array $mapelCols): void
    {
        $nisn = trim($row['nisn'] ?? $row['NISN'] ?? '');
        $nis  = trim($row['nis']  ?? $row['NIS']  ?? '');
        $nama = trim($row['nama_lengkap'] ?? $row['nama'] ?? $row['Nama'] ?? '');

        if ($nisn === '' && $nis === '' && $nama === '') {
            $this->skipped++;
            return;
        }

        $siswa = null;
        if ($nisn !== '') $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa && $nis !== '') $siswa = Siswa::where('nis', $nis)->first();
        if (!$siswa && $nama !== '') $siswa = Siswa::whereRaw('LOWER(nama_lengkap) = ?', [strtolower($nama)])->first();

        if (!$siswa) {
            $id = $nisn ?: $nis ?: $nama ?: "baris {$lineNum}";
            $this->warnings[] = "Baris {$lineNum}: Siswa '{$id}' tidak ditemukan di database, dilewati.";
            $this->skipped++;
            return;
        }

        $kelas = trim($row['kelas'] ?? $row['Kelas'] ?? $siswa->kelas ?? '');
        $saved = 0;

        foreach ($mapelCols as $mapel) {
            $nilaiRaw = $row[$mapel] ?? null;
            if ($nilaiRaw === null || $nilaiRaw === '') continue;

            $nilai = is_numeric($nilaiRaw) ? (float) $nilaiRaw : null;

            if ($nilai !== null && ($nilai < 0 || $nilai > 100)) {
                $this->warnings[] = "Baris {$lineNum} ({$siswa->nama_lengkap}) [{$mapel}]: nilai {$nilai} di luar 0–100, dilewati.";
                continue;
            }

            try {
                NilaiRapor::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $ta, 'semester' => $sem, 'mata_pelajaran' => trim($mapel)],
                    ['kelas' => $kelas, 'kelompok' => 'Umum', 'nilai' => $nilai]
                );
                $saved++;
            } catch (\Throwable $e) {
                $this->errors[] = "Baris {$lineNum} ({$siswa->nama_lengkap}) [{$mapel}]: " . $e->getMessage();
                Log::error('NilaiRaporImport: ' . $e->getMessage());
            }
        }

        $saved > 0 ? $this->imported++ : $this->skipped++;
    }

    public function getImportedCount(): int { return $this->imported; }
    public function getSkippedCount(): int  { return $this->skipped; }
    public function getErrors(): array      { return $this->errors; }
    public function getWarnings(): array    { return $this->warnings; }
    public function hasErrors(): bool       { return count($this->errors) > 0; }
}
