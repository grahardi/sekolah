<?php

namespace App\Imports;

use App\Models\Siswa;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import Nomor Ijazah dari file export "Manajemen Ijazah Dapodik" / Penerbitan
 * NIN. Formatnya sederhana: 2 kolom (NISN, nomor_seri_ijazah), header di
 * baris 1, data mulai baris 2. Cocokkan by NISN ke siswa yg SUDAH ADA
 * (gak bikin siswa baru - beda dari DapodikImport).
 */
class ImportNomorIjazah
{
    protected int $updated = 0;
    protected int $skipped = 0;
    protected array $errors = [];
    protected array $warnings = [];

    public function import(string $filePath): void
    {
        if (! file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan: {$filePath}";
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membaca file: ' . $e->getMessage();
            return;
        }

        $highestRow = $sheet->getHighestDataRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nisn = trim((string) $sheet->getCell('A' . $row)->getFormattedValue());
            $noIjazah = trim((string) $sheet->getCell('B' . $row)->getFormattedValue());

            if ($nisn === '' && $noIjazah === '') continue;

            if ($nisn === '' || $noIjazah === '') {
                $this->warnings[] = "Baris {$row}: NISN atau nomor ijazah kosong, dilewati.";
                $this->skipped++;
                continue;
            }

            $siswa = Siswa::withoutGlobalScopes()->where('nisn', $nisn)->first();

            if (! $siswa) {
                $this->warnings[] = "Baris {$row}: tidak ada siswa dengan NISN \"{$nisn}\" di data kita, dilewati.";
                $this->skipped++;
                continue;
            }

            $siswa->update(['no_ijazah' => $noIjazah]);
            $this->updated++;
        }
    }

    public function getUpdatedCount(): int { return $this->updated; }
    public function getSkippedCount(): int { return $this->skipped; }
    public function getErrors(): array { return $this->errors; }
    public function getWarnings(): array { return $this->warnings; }
}
