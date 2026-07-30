<?php

namespace App\Imports;

use App\Models\Siswa;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SiswaImport
{
    protected array $errors   = [];
    protected array $warnings = [];
    protected int   $imported = 0;
    protected int   $skipped  = 0;

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
            Log::error('SiswaImport file error: ' . $e->getMessage());
        }
    }

    private function processRow(array $row): void
    {
        $rowNum = $this->imported + $this->skipped + count($this->errors) + 1;

        $nisn = trim($row['nisn'] ?? '');
        $nama = trim($row['nama_lengkap'] ?? '');

        if ($nisn === '' && $nama === '') {
            $this->skipped++;
            return;
        }
        if ($nisn === '') {
            $this->warnings[] = "Baris {$rowNum} ({$nama}): NISN kosong, dilewati.";
            $this->skipped++;
            return;
        }
        if ($nama === '') {
            $this->warnings[] = "Baris {$rowNum} (NISN {$nisn}): Nama kosong, dilewati.";
            $this->skipped++;
            return;
        }

        try {
            $tgl    = $this->parseDate($row['tanggal_lahir'] ?? null);
            $agama  = $this->normalizeAgama($row['agama'] ?? 'Islam');

            $goldar = strtoupper(trim($row['golongan_darah'] ?? ''));
            if (!in_array($goldar, ['A','B','AB','O'])) $goldar = 'Tidak Tahu';

            $status = strtolower(trim($row['status'] ?? 'aktif'));
            if (!in_array($status, ['aktif','lulus','keluar','pindah'])) $status = 'aktif';

            $jk = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
            $jk = $jk === 'P' ? 'P' : 'L';

            Siswa::updateOrCreate(
                ['nisn' => $nisn],
                array_filter([
                    'nis'              => $row['nis'] ?? null,
                    'nama_lengkap'     => $nama,
                    'jenis_kelamin'    => $jk,
                    'tempat_lahir'     => trim($row['tempat_lahir'] ?? ''),
                    'tanggal_lahir'    => $tgl,
                    'agama'            => $agama,
                    'alamat'           => trim($row['alamat'] ?? ''),
                    'rt'               => $row['rt'] ?? null,
                    'rw'               => $row['rw'] ?? null,
                    'dusun'            => $row['dusun'] ?? null,
                    'kelurahan'        => $row['kelurahan'] ?? null,
                    'kecamatan'        => $row['kecamatan'] ?? null,
                    'kode_pos'         => $row['kode_pos'] ?? null,
                    'lintang'          => is_numeric($row['lintang'] ?? '') ? $row['lintang'] : null,
                    'bujur'            => is_numeric($row['bujur'] ?? '') ? $row['bujur'] : null,
                    'no_telepon'       => $row['no_telepon'] ?? null,
                    'email'            => $row['email'] ?? null,
                    'nik'              => $row['nik'] ?? null,
                    'no_kk'            => $row['no_kk'] ?? null,
                    'kelas'            => trim($row['kelas'] ?? ''),
                    'rombel'           => $row['rombel'] ?? null,
                    'tahun_masuk'      => is_numeric($row['tahun_masuk'] ?? '') ? (int)$row['tahun_masuk'] : date('Y'),
                    'status'           => $status,
                    'asal_sekolah'     => $row['asal_sekolah'] ?? null,
                    'no_sttb_sd'       => $row['no_sttb_sd'] ?? null,
                    'no_un_sd'         => $row['no_un_sd'] ?? null,
                    'anak_ke'          => is_numeric($row['anak_ke'] ?? '') ? (int)$row['anak_ke'] : null,
                    'golongan_darah'   => $goldar,
                    'tinggi_badan'     => is_numeric($row['tinggi_badan'] ?? '') ? (int)$row['tinggi_badan'] : null,
                    'berat_badan'      => is_numeric($row['berat_badan'] ?? '') ? (int)$row['berat_badan'] : null,
                    'nama_ayah'        => $row['nama_ayah'] ?? null,
                    'tahun_lahir_ayah' => is_numeric($row['tahun_lahir_ayah'] ?? '') ? (int)$row['tahun_lahir_ayah'] : null,
                    'pendidikan_ayah'  => $row['pendidikan_ayah'] ?? null,
                    'pekerjaan_ayah'   => $row['pekerjaan_ayah'] ?? null,
                    'penghasilan_ayah' => $row['penghasilan_ayah'] ?? null,
                    'nama_ibu'         => $row['nama_ibu'] ?? null,
                    'tahun_lahir_ibu'  => is_numeric($row['tahun_lahir_ibu'] ?? '') ? (int)$row['tahun_lahir_ibu'] : null,
                    'pendidikan_ibu'   => $row['pendidikan_ibu'] ?? null,
                    'pekerjaan_ibu'    => $row['pekerjaan_ibu'] ?? null,
                    'penghasilan_ibu'  => $row['penghasilan_ibu'] ?? null,
                    'nama_wali'        => $row['nama_wali'] ?? null,
                    'pekerjaan_wali'   => $row['pekerjaan_wali'] ?? null,
                    'no_telepon_ortu'  => $row['no_telepon_ortu'] ?? null,
                    'alamat_ortu'      => $row['alamat_ortu'] ?? null,
                ], fn($v) => $v !== null && $v !== '')
            );
            $this->imported++;

        } catch (\Throwable $e) {
            $msg = "Baris {$rowNum} (NISN {$nisn}): " . $e->getMessage();
            $this->errors[] = $msg;
            Log::error('SiswaImport row error: ' . $msg);
        }
    }

    private function normalizeAgama(?string $val): string
    {
        $map = [
            'islam'      => 'Islam',
            'kristen'    => 'Kristen',
            'katholik'   => 'Katolik',
            'katolik'    => 'Katolik',
            'catholic'   => 'Katolik',
            'hindu'      => 'Hindu',
            'buddha'     => 'Buddha',
            'budha'      => 'Buddha',
            'konghucu'   => 'Konghucu',
            'kong hu cu' => 'Konghucu',
            'protestan'  => 'Kristen',
        ];
        $key = strtolower(trim($val ?? 'islam'));
        return $map[$key] ?? 'Islam';
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
        if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $val)) {
            try { return Carbon::createFromFormat('d-m-Y', $val)->format('Y-m-d'); } catch (\Throwable $e) {}
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
