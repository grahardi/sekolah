<?php

namespace App\Console\Commands;

use App\Models\Sekolah;
use App\Services\SurveyTemplateBk;
use Illuminate\Console\Command;

class SeedDefaultSurvey extends Command
{
    protected $signature = 'survey:seed-default {--sekolah_id= : ID sekolah spesifik saja, kosongkan utk semua sekolah}';
    protected $description = 'Tambahkan survey DCM default (sesuai jenjang) ke sekolah yang sudah terdaftar, termasuk sekolah demo';

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

        $dibuat = 0;
        $dilewati = 0;

        foreach ($sekolahs as $sekolah) {
            $survey = SurveyTemplateBk::createDefaultSurveyFor($sekolah);

            if ($survey) {
                $this->info("✓ Dibuat untuk: {$sekolah->nama} -> \"{$survey->judul}\"");
                $dibuat++;
            } else {
                $this->line("- Dilewati (sudah ada): {$sekolah->nama}");
                $dilewati++;
            }
        }

        $this->newLine();
        $this->info("Selesai. {$dibuat} survey baru dibuat, {$dilewati} sekolah dilewati (sudah punya template).");

        return self::SUCCESS;
    }
}
