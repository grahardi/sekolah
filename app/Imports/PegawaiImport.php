<?php

namespace App\Imports;

use App\Models\Pegawai;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PegawaiImport
{
    protected array $errors   = [];
    protected array $warnings = [];
    protected int   $imported = 0;
    protected int   $skipped  = 0;

    private const JENIS_VALID = ['PNS', 'PPPK', 'GTT', 'PTT', 'GTY', 'PTY', 'Lainnya'];
    private const STATUS_VALID = ['Aktif', 'Cuti', 'Nonaktif', 'Pensiun', 'Pindah'];

    public function import(string $filePath): void
    {
        if (!file_exists($filePath)) {
            $this->errors[] = "File tidak ditemukan: {$filePath}";
            return;
        }

        try {
            (new FastExcel)->import($filePath, function (array $row) {
                $this->processRow($row);
                return null;
            });
        } catch (\Throwable $e) {
            $this->errors[] = 'Gagal membaca file: ' . $e->getMessage();
            Log::error('PegawaiImport file error: ' . $e->getMessage());
        }
    }

    private function processRow(array $row): void
    {
        $rowNum = $this->imported + $this->skipped + count($this->errors) + 1;

        $nama = trim($row['nama_lengkap'] ?? '');
        $nip  = trim($row['nip_nuptk'] ?? '');

        if ($nama === '' && $nip === '') {
            $this->skipped++;
            return;
        }
        if ($nama === '') {
            $this->warnings[] = "Baris {$rowNum}: Nama kosong, dilewati.";
            $this->skipped++;
            return;
        }

        try {
            $jk = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
            $jk = $jk === 'P' ? 'P' : 'L';

            $jenis = trim($row['jenis_kepegawaian'] ?? 'GTT');
            if (!in_array($jenis, self::JENIS_VALID)) $jenis = 'Lainnya';

            $status = trim($row['status_aktif'] ?? 'Aktif');
            if (!in_array($status, self::STATUS_VALID)) $status = 'Aktif';

            // Kalau NIP diisi, cocokkan berdasarkan itu (update kalau sudah ada).
            // Kalau NIP kosong (non-ASN honorer sering tidak punya NIP), selalu
            // buat baris baru supaya tidak menimpa pegawai lain yang NIP-nya sama-sama kosong.
            $match = $nip !== '' ? ['nip_nuptk' => $nip] : ['id' => 0];

            Pegawai::updateOrCreate(
                $match,
                array_filter([
                    'nip_nuptk' => $nip ?: null,
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => $jk,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                    'jenis_kepegawaian' => $jenis,
                    'jabatan' => $row['jabatan'] ?? null,
                    'unit_kerja' => $row['unit_kerja'] ?? null,
                    'golongan' => $row['golongan'] ?? null,
                    'pangkat' => $row['pangkat'] ?? null,
                    'tmt_cpns' => $this->parseDate($row['tmt_cpns'] ?? null),
                    'tmt_pns' => $this->parseDate($row['tmt_pns'] ?? null),
                    'no_sk_pangkat' => $row['no_sk_pangkat'] ?? null,
                    'tmt_pangkat_terakhir' => $this->parseDate($row['tmt_pangkat_terakhir'] ?? null),
                    'tmt_gaji_berkala_terakhir' => $this->parseDate($row['tmt_gaji_berkala_terakhir'] ?? null),
                    'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
                    'no_hp' => $row['no_hp'] ?? null,
                    'email' => $row['email'] ?? null,
                    'alamat' => $row['alamat'] ?? null,
                    'status_aktif' => $status,
                    'tanggal_masuk' => $this->parseDate($row['tanggal_masuk'] ?? null),
                ], fn ($v) => $v !== null && $v !== '')
            );
            $this->imported++;
        } catch (\Throwable $e) {
            $msg = "Baris {$rowNum} ({$nama}): " . $e->getMessage();
            $this->errors[] = $msg;
            Log::error('PegawaiImport row error: ' . $msg);
        }
    }

    private function parseDate(?string $val): ?string
    {
        if (empty($val)) return null;
        $val = trim($val);

        if (is_numeric($val)) {
            try { return Carbon::createFromTimestamp(($val - 25569) * 86400)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $val)) {
            try { return Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

        try { return Carbon::parse($val)->format('Y-m-d'); } catch (\Throwable $e) {}
        return null;
    }

    public function getImportedCount(): int  { return $this->imported; }
    public function getSkippedCount(): int   { return $this->skipped; }
    public function getErrors(): array       { return $this->errors; }
    public function getWarnings(): array     { return $this->warnings; }
    public function hasErrors(): bool        { return count($this->errors) > 0; }
}
