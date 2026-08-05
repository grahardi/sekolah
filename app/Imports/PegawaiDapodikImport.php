<?php

namespace App\Imports;

use App\Models\Pegawai;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Import khusus format export "Daftar Guru/PTK" dari Dapodik (BUKAN template
 * Excel kita sendiri - beda struktur, header ada di baris ke-5, bukan baris 1).
 * Kolom diverifikasi dari file asli SMP Negeri 1 Turen.
 */
class PegawaiDapodikImport
{
    protected array $errors   = [];
    protected array $warnings = [];
    protected int   $imported = 0;
    protected int   $skipped  = 0;

    // Kolom (huruf) sesuai urutan export Dapodik "Daftar Guru"
    private const COL_NAMA               = 'B';
    private const COL_NUPTK              = 'C';
    private const COL_JK                 = 'D';
    private const COL_TEMPAT_LAHIR       = 'E';
    private const COL_TANGGAL_LAHIR      = 'F';
    private const COL_NIP                = 'G';
    private const COL_STATUS_KEPEGAWAIAN = 'H';
    private const COL_JENIS_PTK          = 'I';
    private const COL_NIK                = 'J'; // asumsi kolom J = NIK (di antara Jenis PTK & Alamat) - cek lagi kalau ternyata beda
    private const COL_ALAMAT_JALAN       = 'K';
    private const COL_RT                 = 'L';
    private const COL_RW                 = 'M';
    private const COL_DUSUN              = 'N';
    private const COL_DESA               = 'O';
    private const COL_KECAMATAN          = 'P';
    private const COL_HP                 = 'S';
    private const COL_EMAIL              = 'T';
    private const COL_TUGAS_TAMBAHAN     = 'U';
    private const COL_TANGGAL_CPNS       = 'W';
    private const COL_SK_PENGANGKATAN    = 'X';
    private const COL_TMT_PENGANGKATAN   = 'Y';
    private const COL_PANGKAT_GOLONGAN   = 'AA';
    private const COL_TMT_PNS            = 'AH';

    public function import(string $filePath): void
    {
        if (! file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan: {$filePath}";
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $headerRow = $this->cariBarisHeader($sheet);
            if (! $headerRow) {
                $this->errors[] = 'Format tidak dikenali - baris header (Nama, NUPTK, JK, dst) tidak ditemukan. Pastikan ini file export "Daftar Guru/PTK" asli dari Dapodik.';
                return;
            }

            $lastRow = $sheet->getHighestRow();
            for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                $this->processRow($sheet, $row);
            }
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membaca file: ' . $e->getMessage();
            Log::error('PegawaiDapodikImport error: ' . $e->getMessage());
        }
    }

    /** Cari baris yang kolom B = "Nama" dan kolom C = "NUPTK" (bukan selalu baris 5 persis) */
    private function cariBarisHeader($sheet): ?int
    {
        for ($row = 1; $row <= 10; $row++) {
            $b = trim((string) $sheet->getCell(self::COL_NAMA . $row)->getValue());
            $c = trim((string) $sheet->getCell(self::COL_NUPTK . $row)->getValue());
            if (strcasecmp($b, 'Nama') === 0 && strcasecmp($c, 'NUPTK') === 0) {
                return $row;
            }
        }
        return null;
    }

    private function val($sheet, string $col, int $row): ?string
    {
        $v = $sheet->getCell($col . $row)->getValue();
        if ($v === null) return null;
        $v = trim((string) $v);
        return $v === '' || strtolower($v) === 'nan' ? null : $v;
    }

    private function processRow($sheet, int $row): void
    {
        $nama = $this->val($sheet, self::COL_NAMA, $row);
        if (! $nama) {
            $this->skipped++;
            return;
        }

        try {
            $nip = $this->val($sheet, self::COL_NIP, $row);
            $nuptk = $this->val($sheet, self::COL_NUPTK, $row);
            $identifier = $nip ?: $nuptk; // NIP diutamakan, fallback NUPTK

            $jk = strtoupper($this->val($sheet, self::COL_JK, $row) ?? 'L');
            $jk = $jk === 'P' ? 'P' : 'L';

            $statusMentah = $this->val($sheet, self::COL_STATUS_KEPEGAWAIAN, $row) ?? '';
            $jenisKepegawaian = $this->mapJenisKepegawaian($statusMentah);

            // Jabatan diambil dari "Tugas Tambahan" (lebih spesifik drpd "Jenis PTK"
            // yg biasanya cuma "Guru" generik utk semua baris)
            $jabatan = $this->val($sheet, self::COL_TUGAS_TAMBAHAN, $row)
                ?? $this->val($sheet, 'I', $row); // fallback ke Jenis PTK kalau kosong

            $alamat = $this->susunAlamat($sheet, $row);

            $golongan = $this->val($sheet, self::COL_PANGKAT_GOLONGAN, $row);

            $match = $identifier ? ['nip_nuptk' => $identifier] : ['id' => 0];

            Pegawai::updateOrCreate($match, array_filter([
                'nip_nuptk' => $identifier,
                'nik' => $this->val($sheet, self::COL_NIK, $row),
                'nama_lengkap' => $nama,
                'jenis_kelamin' => $jk,
                'tempat_lahir' => $this->val($sheet, self::COL_TEMPAT_LAHIR, $row),
                'tanggal_lahir' => $this->parseDate($this->val($sheet, self::COL_TANGGAL_LAHIR, $row)),
                'jenis_kepegawaian' => $jenisKepegawaian,
                'jabatan' => $jabatan,
                'golongan' => $golongan,
                'tmt_cpns' => $this->parseDate($this->val($sheet, self::COL_TANGGAL_CPNS, $row)),
                'tmt_pns' => $this->parseDate($this->val($sheet, self::COL_TMT_PNS, $row)),
                'no_sk_pangkat' => $this->val($sheet, self::COL_SK_PENGANGKATAN, $row),
                'tmt_pangkat_terakhir' => $this->parseDate($this->val($sheet, self::COL_TMT_PENGANGKATAN, $row)),
                'no_hp' => $this->val($sheet, self::COL_HP, $row),
                'email' => $this->val($sheet, self::COL_EMAIL, $row),
                'alamat' => $alamat,
                'status_aktif' => 'Aktif',
            ], fn ($v) => $v !== null && $v !== ''));

            $this->imported++;
        } catch (\Throwable $e) {
            $this->errors[] = "Baris {$row} ({$nama}): " . $e->getMessage();
            Log::error('PegawaiDapodikImport row error: ' . $e->getMessage());
        }
    }

    private function susunAlamat($sheet, int $row): ?string
    {
        $jalan = $this->val($sheet, self::COL_ALAMAT_JALAN, $row);
        $rt = $this->val($sheet, self::COL_RT, $row);
        $rw = $this->val($sheet, self::COL_RW, $row);
        $dusun = $this->val($sheet, self::COL_DUSUN, $row);
        $desa = $this->val($sheet, self::COL_DESA, $row);
        $kec = $this->val($sheet, self::COL_KECAMATAN, $row);

        $parts = array_filter([
            $jalan,
            ($rt || $rw) ? "RT {$rt}/RW {$rw}" : null,
            $dusun,
            $desa,
            $kec,
        ]);

        return count($parts) > 0 ? implode(', ', $parts) : null;
    }

    private function mapJenisKepegawaian(string $status): string
    {
        $s = strtoupper($status);
        if (str_contains($s, 'PPPK')) return 'PPPK'; // termasuk "PPPK Paruh Waktu"
        if (str_contains($s, 'PNS')) return 'PNS';
        if (str_contains($s, 'GTY')) return 'GTY';
        if (str_contains($s, 'PTY')) return 'PTY';
        if (str_contains($s, 'HONORER') || str_contains($s, 'GTT')) return 'GTT';
        if (str_contains($s, 'PTT')) return 'PTT';
        return 'Lainnya';
    }

    private function parseDate(?string $val): ?string
    {
        if (empty($val)) return null;
        $val = trim($val);

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $val)) {
            return substr($val, 0, 10);
        }
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $val)) {
            try { return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        try { return Carbon::parse($val)->format('Y-m-d'); } catch (\Throwable $e) {}
        return null;
    }

    public function getImportedCount(): int  { return $this->imported; }
    public function getSkippedCount(): int   { return $this->skipped; }
    public function getErrors(): array       { return $this->errors; }
    public function getWarnings(): array     { return $this->warnings; }
}
