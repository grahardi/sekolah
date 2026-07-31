<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use App\Services\MataPelajaranTemplate;
use Illuminate\Console\Command;

class SeedDefaultMapel extends Command
{
    protected $signature = 'mapel:seed-default {--sekolah_id= : ID sekolah spesifik saja, kosongkan utk semua sekolah}';
    protected $description = 'Tambahkan mata pelajaran standar Kurikulum Merdeka (sesuai jenjang SD/SMP/SMA) ke sekolah yang sudah terdaftar';

    public function handle(): int
    {
        $sekolahId = $this->option('sekolah_id');

        $sekolahs = $sekolahId
            ? Sekolah::where('id', $sekolahId)->get()
            : Sekolah::all();

        if ($sekolahs->isEmpty()) {
            $this->error('Tidak ada sekolah ditemukan.');
            return self::FAILURE;
        }

        $totalDibuat = 0;

        foreach ($sekolahs as $sekolah) {
            $jumlah = MataPelajaranTemplate::seedFor($sekolah);
            $totalDibuat += $jumlah;

            if ($jumlah > 0) {
                $this->info("✓ {$sekolah->nama}: {$jumlah} mapel baru ditambahkan");
            } else {
                $this->line("- {$sekolah->nama}: sudah lengkap, dilewati");
            }
        }

        $this->newLine();
        $this->info("Selesai. Total {$totalDibuat} mata pelajaran baru dibuat di " . $sekolahs->count() . ' sekolah.');

        return self::SUCCESS;
    }
}
