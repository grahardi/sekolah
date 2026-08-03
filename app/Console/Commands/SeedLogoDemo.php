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
        $sumberHeader = resource_path('seed-assets/header-demo-konoha.png');
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

            $update = ['logo_kabupaten' => $tujuan, 'rapor_tampilkan_logo' => true];

            if (file_exists($sumberHeader)) {
                $tujuanHeader = 'kop-surat/header-demo-' . $sekolah->id . '.png';
                Storage::disk('public')->put($tujuanHeader, file_get_contents($sumberHeader));
                $update['rapor_header_custom'] = $tujuanHeader;
                $update['rapor_pakai_header_custom'] = true;
                $update['rapor_header_custom_scale'] = 100;
            }

            $sekolah->update($update);

            $this->info("✓ Logo & header demo dipasang untuk: {$sekolah->nama}");
        }

        return self::SUCCESS;
    }
}
