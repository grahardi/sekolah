<?php

namespace App\Console\Commands;

use App\Models\ArsipBerkas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class KonversiJpgKePdf extends Command
{
    protected $signature = 'berkas:konversi-jpg-ke-pdf {--dry-run : Cuma tampilkan rencana, gak benar-benar convert apa pun}';

    protected $description = 'Convert semua berkas dokumen (KK/Akta/Ijazah/dst + Berkas Lain) yg masih JPG/PNG jadi PDF, biar seragam. Foto profil DIKECUALIKAN (tetap gambar, dipakai utk avatar/kartu pelajar)';

    // Field dokumen yg dikonversi - SENGAJA tidak termasuk 'foto'
    private const FIELD_DOKUMEN = ['kartu_keluarga', 'akta_lahir', 'ijazah_sd', 'ijazah', 'transkrip_nilai', 'sertifikat_tka'];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('=== MODE DRY-RUN - tidak ada file yg benar-benar dikonversi ===');
        }

        $semuaArsip = ArsipBerkas::all();
        $this->info("Memeriksa {$semuaArsip->count()} data arsip berkas...");
        $this->newLine();

        $dikonversi = 0; $dilewati = 0; $gagal = 0;

        foreach ($semuaArsip as $arsip) {
            $namaSiswa = $arsip->siswa?->nama_lengkap ?? "ID Siswa {$arsip->siswa_id}";
            $adaPerubahan = false;

            // 1. Field dokumen utama (KK, Akta, dst)
            foreach (self::FIELD_DOKUMEN as $field) {
                $path = $arsip->{$field};
                if (! $path || ! $this->apakahGambar($path)) continue;

                $this->line("<fg=cyan>{$namaSiswa}</> - {$field}: {$path}");

                if (! $dryRun) {
                    $pathBaru = $this->konversiSatuFile($path);
                    if ($pathBaru) {
                        $arsip->{$field} = $pathBaru;
                        $adaPerubahan = true;
                        $dikonversi++;
                    } else {
                        $gagal++;
                    }
                } else {
                    $dikonversi++;
                }
            }

            // 2. Berkas Lain (array)
            if ($arsip->berkas_lain) {
                $daftarBaru = $arsip->berkas_lain;
                foreach ($daftarBaru as $i => $item) {
                    if (! $this->apakahGambar($item['path'])) continue;

                    $this->line("<fg=cyan>{$namaSiswa}</> - Berkas Lain #{$i}: {$item['path']}");

                    if (! $dryRun) {
                        $pathBaru = $this->konversiSatuFile($item['path']);
                        if ($pathBaru) {
                            $daftarBaru[$i]['path'] = $pathBaru;
                            $adaPerubahan = true;
                            $dikonversi++;
                        } else {
                            $gagal++;
                        }
                    } else {
                        $dikonversi++;
                    }
                }
                if (! $dryRun) $arsip->berkas_lain = $daftarBaru;
            }

            if ($adaPerubahan && ! $dryRun) {
                $arsip->save();
            }
        }

        $this->newLine();
        $this->info('=== RINGKASAN ===');
        $this->table(['Keterangan', 'Jumlah'], [
            ['Berhasil dikonversi', $dikonversi],
            ['Gagal', $gagal],
        ]);

        if ($dryRun) {
            $this->newLine();
            $this->warn('Ini baru DRY-RUN. Jalankan tanpa --dry-run kalau hasilnya sudah sesuai.');
        }

        return self::SUCCESS;
    }

    private function apakahGambar(string $path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']);
    }

    /** Convert 1 file gambar jadi PDF (pakai DomPDF, gambar full-page), hapus file gambar asli. Return path PDF baru atau null kalau gagal */
    private function konversiSatuFile(string $pathGambar): ?string
    {
        try {
            if (! Storage::disk('public')->exists($pathGambar)) {
                $this->error("    File tidak ditemukan: {$pathGambar}");
                return null;
            }

            $urlGambar = storage_path('app/public/' . $pathGambar);
            [$lebarPx, $tinggiPx] = @getimagesize($urlGambar) ?: [600, 800];

            // Skala ke ukuran A4 proporsional, gambar full-page tanpa margin
            $rasio = $tinggiPx / $lebarPx;
            $lebarMm = 190; // A4 portrait dikurangi margin kecil
            $tinggiMm = $lebarMm * $rasio;

            $html = '<html><head><style>*{margin:0;padding:0;}</style></head><body>'
                . '<img src="' . $urlGambar . '" style="width:' . $lebarMm . 'mm;height:' . $tinggiMm . 'mm;">'
                . '</body></html>';

            $pdf = Pdf::loadHTML($html);
            $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
            $pdf->setPaper('a4', 'portrait');

            $pathBaru = preg_replace('/\.[^.]+$/', '.pdf', $pathGambar);
            Storage::disk('public')->put($pathBaru, $pdf->output());

            Storage::disk('public')->delete($pathGambar);

            return $pathBaru;
        } catch (\Throwable $e) {
            $this->error("    Gagal convert {$pathGambar}: " . $e->getMessage());
            return null;
        }
    }
}
