<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SeedLogoDemo extends Command
{
    protected $signature = 'sekolah:seed-logo-demo';
    protected $description = 'Pasang logo kabupaten contoh (Kabupaten Malang) ke sekolah demo, buat contoh tampilan kop surat rapor';

    public function handle(): int
    {
        $sumber = resource_path('seed-assets/logo-kabupaten-malang.png');
        if (! file_exists($sumber)) {
            $this->error('File logo contoh tidak ditemukan di resources/seed-assets/. Pastikan sudah git pull terbaru.');
            return self::FAILURE;
        }

        $sekolahs = Sekolah::where('is_demo', true)->get();
        if ($sekolahs->isEmpty()) {
            $this->warn('Tidak ada sekolah demo (is_demo=true) ditemukan.');
            return self::SUCCESS;
        }

        foreach ($sekolahs as $sekolah) {
            $tujuan = 'kop-surat/logo-kabupaten-demo-' . $sekolah->id . '.png';
            Storage::disk('public')->put($tujuan, file_get_contents($sumber));

            $sekolah->update([
                'logo_kabupaten' => $tujuan,
                'rapor_tampilkan_logo' => true,
            ]);

            $this->info("✓ Logo dipasang untuk: {$sekolah->nama}");
        }

        return self::SUCCESS;
    }
}
