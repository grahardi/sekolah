<?php

namespace App\Imports;

use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import langsung dari file "Daftar Peserta Didik" hasil unduhan Dapodik
 * (bukan template kita sendiri). Struktur file ini standar dari Kemendikdasmen:
 * - 4 baris info sekolah di atas (nama sekolah, kecamatan, tanggal unduh, dst)
 * - Baris 5 & 6: header (sebagian kolom seperti "Data Ayah/Ibu/Wali" digabung
 *   2 baris - baris 5 nama kelompok, baris 6 nama field)
 * - Baris 7 dst: data siswa
 *
 * Karena strukturnya beda total dari template kita (bukan satu baris header
 * sederhana), file ini dibaca langsung pakai PhpSpreadsheet berdasarkan posisi
 * kolom (bukan FastExcel yang asumsi header sebaris di baris pertama).
 */
class DapodikImport
{
    protected array $errors = [];
    protected array $warnings = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    private const HEADER_ROW = 5;
    private const DATA_START_ROW = 7;

    public function import(string $filePath): void
    {
        if (! file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan: {$filePath}";
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('Daftar Peserta Didik') ?? $spreadsheet->getActiveSheet();
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membaca file: ' . $e->getMessage() . '. Pastikan ini file asli hasil unduhan Dapodik (menu Peserta Didik > Unduh).';
            Log::error('DapodikImport file error: ' . $e->getMessage());
            return;
        }

        // Validasi ringan: pastikan ini memang format Dapodik, bukan file lain
        $headerCheck = $sheet->getCell('B5')->getValue();
        if (trim((string) $headerCheck) !== 'Nama') {
            $this->errors[] = 'Format file tidak dikenali sebagai unduhan Dapodik "Daftar Peserta Didik". Gunakan menu import template biasa kalau file ini bukan dari Dapodik.';
            return;
        }

        $highestRow = $sheet->getHighestDataRow();

        for ($row = self::DATA_START_ROW; $row <= $highestRow; $row++) {
            $get = fn (string $col) => trim((string) $sheet->getCell($col . $row)->getFormattedValue());
            $getRaw = fn (string $col) => $sheet->getCell($col . $row)->getValue();

            $nama = $get('B');
            $nisn = $get('E');

            if ($nama === '' && $nisn === '') {
                continue; // baris kosong, lewati diam-diam
            }

            $rowNum = $row - self::DATA_START_ROW + 1;

            if ($nisn === '') {
                $this->warnings[] = "Baris {$rowNum} ({$nama}): NISN kosong di Dapodik, dilewati.";
                $this->skipped++;
                continue;
            }

            try {
                $rombelRaw = $get('AQ'); // "Rombel Saat Ini", contoh: "7 F"
                [$kelas, $rombel] = $this->splitRombel($rombelRaw);

                Siswa::updateOrCreate(
                    ['nisn' => $nisn],
                    array_filter([
                        'nis' => $get('C') ?: null,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin' => strtoupper($get('D')) === 'P' ? 'P' : 'L',
                        'tempat_lahir' => $get('F') ?: null,
                        'tanggal_lahir' => $this->parseDate($getRaw('G')),
                        'nik' => $get('H') ?: null,
                        'agama' => $this->normalizeAgama($get('I')),
                        'alamat' => $get('J') ?: null,
                        'rt' => $get('K') ?: null,
                        'rw' => $get('L') ?: null,
                        'dusun' => $get('M') ?: null,
                        'kelurahan' => $get('N') ?: null,
                        'kecamatan' => $get('O') ?: null,
                        'kode_pos' => $get('P') ?: null,
                        'no_telepon' => $get('T') ?: ($get('S') ?: null), // HP diutamakan, fallback Telepon
                        'email' => $get('U') ?: null,

                        // Data Ayah (Y-AD)
                        'nama_ayah' => $get('Y') ?: null,
                        'tahun_lahir_ayah' => is_numeric($get('Z')) ? (int) $get('Z') : null,
                        'pendidikan_ayah' => $get('AA') ?: null,
                        'pekerjaan_ayah' => $get('AB') ?: null,
                        'penghasilan_ayah' => $get('AC') ?: null,
                        'nik_ayah' => $get('AD') ?: null, // asumsi kolom NIK - cek lagi kesesuaiannya

                        // Data Ibu (AE-AJ)
                        'nama_ibu' => $get('AE') ?: null,
                        'tahun_lahir_ibu' => is_numeric($get('AF')) ? (int) $get('AF') : null,
                        'pendidikan_ibu' => $get('AG') ?: null,
                        'pekerjaan_ibu' => $get('AH') ?: null,
                        'penghasilan_ibu' => $get('AI') ?: null,
                        'nik_ibu' => $get('AJ') ?: null, // asumsi kolom NIK - cek lagi kesesuaiannya

                        // Data Wali (AK-AP)
                        'nama_wali' => $get('AK') ?: null,
                        'pekerjaan_wali' => $get('AN') ?: null,

                        'kelas' => $kelas,
                        'rombel' => $rombel,
                        'diterima_di_kelas' => trim("{$kelas} {$rombel}"),
                        'tahun_masuk' => (int) date('Y'),
                        'status' => 'aktif',

                        'asal_sekolah' => $get('BE') ?: null,
                        'anak_ke' => is_numeric($get('BF')) ? (int) $get('BF') : null,
                        'lintang' => is_numeric($get('BG')) ? $get('BG') : null,
                        'bujur' => is_numeric($get('BH')) ? $get('BH') : null,
                        'no_kk' => $get('BI') ?: null,
                        'berat_badan' => is_numeric($get('BJ')) ? (int) $get('BJ') : null,
                        'tinggi_badan' => is_numeric($get('BK')) ? (int) $get('BK') : null,
                    ], fn ($v) => $v !== null && $v !== '')
                );

                $this->imported++;
            } catch (\Throwable $e) {
                $msg = "Baris {$rowNum} (NISN {$nisn}): " . $e->getMessage();
                $this->errors[] = $msg;
                Log::error('DapodikImport row error: ' . $msg);
            }
        }
    }

    /**
     * "Rombel Saat Ini" biasanya berformat "7 F" (kelas + rombel dipisah
     * spasi). Kalau formatnya beda, seluruh teks dianggap sebagai kelas.
     */
    private function splitRombel(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [null, null];
        }
        if (preg_match('/^(\d{1,2})\s*[\.\-]?\s*([A-Za-z0-9]+)$/', $raw, $m)) {
            return [$m[1], $m[2]];
        }
        return [$raw, null];
    }

    private function normalizeAgama(?string $val): string
    {
        $map = [
            'islam' => 'Islam', 'kristen' => 'Kristen', 'protestan' => 'Kristen',
            'katholik' => 'Katolik', 'katolik' => 'Katolik', 'khatolik' => 'Katolik',
            'hindu' => 'Hindu', 'buddha' => 'Buddha', 'budha' => 'Buddha',
            'konghucu' => 'Konghucu', 'kong hu cu' => 'Konghucu',
        ];
        $key = strtolower(trim($val ?? 'islam'));
        return $map[$key] ?? 'Islam';
    }

    private function parseDate(mixed $val): ?string
    {
        if (empty($val)) {
            return null;
        }
        // Cell tanggal Excel biasanya sudah berupa objek DateTime dari PhpSpreadsheet
        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }
        if (is_numeric($val)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }
        try {
            return Carbon::parse((string) $val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
