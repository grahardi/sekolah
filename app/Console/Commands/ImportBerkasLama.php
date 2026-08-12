<?php

namespace App\Console\Commands;

use App\Models\ArsipBerkas;
use App\Models\Siswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportBerkasLama extends Command
{
    protected $signature = 'berkas:import-lama
        {folder : Path folder berisi subfolder per siswa, mis. /home/www/Unduhan_Berkas_smp-afirmasi}
        {--dry-run : Cuma tampilkan rencana, gak benar-benar copy/simpan apa pun}';

    protected $description = 'Import berkas siswa yg tercecer (folder per siswa: Nama_NISN) - cocokkan by NISN lalu nama, klasifikasi KK/Akta/Lainnya';

    public function handle(): int
    {
        $folder = rtrim($this->argument('folder'), '/');
        $dryRun = $this->option('dry-run');

        if (! is_dir($folder)) {
            $this->error("Folder tidak ditemukan: {$folder}");
            return self::FAILURE;
        }

        if ($dryRun) {
            $this->warn('=== MODE DRY-RUN - tidak ada file yg benar-benar di-copy/disimpan ===');
        }

        $subfolders = collect(scandir($folder))
            ->reject(fn ($f) => in_array($f, ['.', '..']))
            ->filter(fn ($f) => is_dir("{$folder}/{$f}"))
            ->values();

        $this->info("Ditemukan {$subfolders->count()} folder siswa. Memproses...");
        $this->newLine();

        $cocokNisn = 0; $cocokNama = 0; $tidakCocok = 0; $totalFileDiproses = 0;
        $tidakCocokList = [];

        foreach ($subfolders as $namaFolder) {
            // Format: NAMA SISWA_NISN - ambil bagian setelah underscore TERAKHIR sbg NISN
            $posUnderscore = strrpos($namaFolder, '_');
            if ($posUnderscore === false) {
                $tidakCocok++;
                $tidakCocokList[] = $namaFolder . ' (format nama folder gak sesuai)';
                continue;
            }

            $namaDariFolder = trim(substr($namaFolder, 0, $posUnderscore));
            $nisnDariFolder = trim(substr($namaFolder, $posUnderscore + 1));

            // 1. Coba cocokkan by NISN dulu
            $siswa = Siswa::withoutGlobalScopes()->where('nisn', $nisnDariFolder)->first();
            $caraCocok = 'NISN';

            // 2. Fallback: cocokkan by nama (case-insensitive)
            if (! $siswa) {
                $siswa = Siswa::withoutGlobalScopes()
                    ->whereRaw('UPPER(nama_lengkap) = ?', [strtoupper($namaDariFolder)])
                    ->first();
                $caraCocok = 'Nama';
            }

            if (! $siswa) {
                $tidakCocok++;
                $tidakCocokList[] = "{$namaFolder} (NISN '{$nisnDariFolder}' & nama '{$namaDariFolder}' gak ketemu di data siswa)";
                continue;
            }

            if ($caraCocok === 'NISN') $cocokNisn++; else $cocokNama++;

            // Ambil semua file, urutkan berdasarkan angka urut di nama file
            // (pola: {timestamp}_{urut}_{nama_asli})
            $pathFolder = "{$folder}/{$namaFolder}";
            $files = collect(scandir($pathFolder))
                ->reject(fn ($f) => in_array($f, ['.', '..']))
                ->filter(fn ($f) => is_file("{$pathFolder}/{$f}"))
                ->sortBy(function ($f) {
                    if (preg_match('/^\d+_(\d+)_/', $f, $m)) return (int) $m[1];
                    return 999;
                })
                ->values();

            $this->line("<fg=cyan>{$siswa->nama_lengkap}</> (NISN {$siswa->nisn}) - cocok via {$caraCocok}, {$files->count()} file");

            // Klasifikasi SEKALI di sini, hasilnya dipakai baik utk copy maupun simpan DB
            $rencana = [];
            $nomorLain = 0;
            foreach ($files as $i => $namaFile) {
                $urutan = $i + 1;
                $namaLower = strtolower($namaFile);
                $ekstensi = pathinfo($namaFile, PATHINFO_EXTENSION);

                if ($urutan === 1 || str_contains($namaLower, 'kk')) {
                    $jenis = 'kartu_keluarga';
                    $namaTujuan = "{$siswa->nisn}_kk.{$ekstensi}";
                } elseif ($urutan === 3 || str_contains($namaLower, 'akta')) {
                    $jenis = 'akta_lahir';
                    $namaTujuan = "{$siswa->nisn}_akta.{$ekstensi}";
                } else {
                    $nomorLain++;
                    $jenis = 'lain';
                    $namaTujuan = "{$siswa->nisn}_berkas{$nomorLain}.{$ekstensi}";
                }

                $tujuanRelatif = "arsip/{$siswa->id}/{$namaTujuan}";
                $this->line("    [{$urutan}] {$namaFile} -> {$jenis} ({$tujuanRelatif})");

                $rencana[] = ['file_asal' => $namaFile, 'jenis' => $jenis, 'path_tujuan' => $tujuanRelatif];
                $totalFileDiproses++;
            }

            if (! $dryRun) {
                foreach ($rencana as $r) {
                    $isi = file_get_contents("{$pathFolder}/{$r['file_asal']}");
                    Storage::disk('public')->put($r['path_tujuan'], $isi);
                }

                $arsip = ArsipBerkas::firstOrNew(['siswa_id' => $siswa->id]);
                $arsip->siswa_id = $siswa->id;
                foreach ($rencana as $r) {
                    if ($r['jenis'] === 'kartu_keluarga') $arsip->kartu_keluarga = $r['path_tujuan'];
                    if ($r['jenis'] === 'akta_lahir') $arsip->akta_lahir = $r['path_tujuan'];
                }
                $arsip->save();

                foreach ($rencana as $r) {
                    if ($r['jenis'] === 'lain') {
                        $arsip->tambahBerkasLain($r['path_tujuan'], $r['file_asal']);
                    }
                }
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info('=== RINGKASAN ===');
        $this->table(['Keterangan', 'Jumlah'], [
            ['Cocok via NISN', $cocokNisn],
            ['Cocok via Nama (fallback)', $cocokNama],
            ['Tidak cocok / dilewati', $tidakCocok],
            ['Total file diproses', $totalFileDiproses],
        ]);

        if ($tidakCocokList) {
            $this->newLine();
            $this->warn('Folder yang TIDAK cocok / dilewati:');
            foreach ($tidakCocokList as $t) {
                $this->line("  - {$t}");
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('Ini baru DRY-RUN. Jalankan tanpa --dry-run kalau hasilnya sudah sesuai.');
        }

        return self::SUCCESS;
    }
}
