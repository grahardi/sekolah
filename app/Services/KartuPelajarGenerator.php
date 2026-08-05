<?php

namespace App\Services;

use App\Models\Siswa;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Interfaces\ImageInterface;

class KartuPelajarGenerator
{
    // Ukuran CR80 (kartu ID standar) @ 300dpi: 85.6mm x 54mm
    const LEBAR = 1013;
    const TINGGI = 638;

    public static function buat(Siswa $siswa, int $model, ?string $barcodePngBinary): ImageInterface
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->create(self::LEBAR, self::TINGGI)->fill('#ffffff');

        $fontReguler = resource_path('fonts/DejaVuSans.ttf');
        $fontBold = resource_path('fonts/DejaVuSans-Bold.ttf');

        $navy = '#1E3A5F';
        $kuning = '#FBBF24';
        $abuTeks = '#475569';
        $hitamTeks = '#1E293B';

        // Border luar
        $img->drawRectangle(2, 2, function ($rect) {
            $rect->size(self::LEBAR - 4, self::TINGGI - 4);
            $rect->border('#cbd5e1', 2);
        });

        $sekolahNama = strtoupper($siswa->sekolah->nama ?? 'SEKOLAH');

        if ($model === 2) {
            // Model 2: Colorful Badge - strip kuning atas, header putih center
            $img->drawRectangle(0, 0, function ($rect) use ($kuning) {
                $rect->size(self::LEBAR, 22);
                $rect->background($kuning);
            });
            $img->text($sekolahNama, self::LEBAR / 2, 60, function ($font) use ($fontBold, $navy) {
                $font->file($fontBold); $font->size(30); $font->color($navy); $font->align('center'); $font->valign('top');
            });
            $img->text('KARTU TANDA PELAJAR', self::LEBAR / 2, 95, function ($font) use ($fontReguler) {
                $font->file($fontReguler); $font->size(18); $font->color('#64748b'); $font->align('center'); $font->valign('top');
            });
            $fotoY = 130; $fotoX = 40; $fotoW = 190; $fotoH = 230;
            self::tempelFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $kuning, 5);
            $infoX = $fotoX + $fotoW + 30;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, true);
        } elseif ($model === 3) {
            // Model 3: Minimalist - bar samping navy
            $img->drawRectangle(0, 0, function ($rect) use ($navy) {
                $rect->size(24, self::TINGGI);
                $rect->background($navy);
            });
            $img->text($sekolahNama, 55, 30, function ($font) use ($fontBold, $hitamTeks) {
                $font->file($fontBold); $font->size(26); $font->color($hitamTeks); $font->valign('top');
            });
            $img->text('KARTU TANDA PELAJAR', 55, 62, function ($font) use ($fontReguler) {
                $font->file($fontReguler); $font->size(16); $font->color('#94a3b8'); $font->valign('top');
            });
            $img->drawLine(function ($line) use ($navy) {
                $line->from(55, 100); $line->to(self::LEBAR - 40, 100); $line->color('#f1f5f9'); $line->width(2);
            });
            $fotoY = 120; $fotoX = 55; $fotoW = 175; $fotoH = 210;
            self::tempelFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, '#f1f5f9', 4);
            $infoX = $fotoX + $fotoW + 28;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, '#2563EB', $hitamTeks, '#64748b', false);
        } else {
            // Model 1 (default): Modern Navy - header solid navy
            $img->drawRectangle(0, 0, function ($rect) use ($navy) {
                $rect->size(self::LEBAR, 78);
                $rect->background($navy);
            });
            $img->text($sekolahNama, 36, 22, function ($font) use ($fontBold) {
                $font->file($fontBold); $font->size(28); $font->color('#ffffff'); $font->valign('top');
            });
            $img->text('KARTU TANDA PELAJAR', 36, 54, function ($font) use ($fontReguler) {
                $font->file($fontReguler); $font->size(16); $font->color('#ffffff'); $font->valign('top');
            });
            $fotoY = 105; $fotoX = 36; $fotoW = 185; $fotoH = 225;
            self::tempelFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, '#cbd5e1', 3);
            $infoX = $fotoX + $fotoW + 30;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, true);
        }

        // Barcode di bawah (semua model)
        if ($barcodePngBinary) {
            $barcodeImg = $manager->read($barcodePngBinary);
            $barcodeImg->resize(self::LEBAR - 80, 70);
            $img->place($barcodeImg, 'bottom', 0, 34);
            $img->text($siswa->nis ?: $siswa->nisn, self::LEBAR / 2, self::TINGGI - 24, function ($font) use ($fontBold, $hitamTeks) {
                $font->file($fontBold); $font->size(16); $font->color($hitamTeks); $font->align('center'); $font->valign('bottom');
            });
        }

        return $img;
    }

    private static function tempelFoto(ImageInterface $img, Siswa $siswa, int $x, int $y, int $w, int $h, string $borderColor, int $borderW): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $foto = $manager->read($siswa->foto_url);
            $foto->cover($w, $h);
        } catch (\Throwable $e) {
            $manager = new ImageManager(new Driver());
            $foto = $manager->create($w, $h)->fill('#e2e8f0');
        }
        $img->drawRectangle($x - $borderW, $y - $borderW, function ($rect) use ($w, $h, $borderW, $borderColor) {
            $rect->size($w + $borderW * 2, $h + $borderW * 2);
            $rect->background($borderColor);
        });
        $img->place($foto, 'top-left', $x, $y);
    }

    private static function tulisInfo(ImageInterface $img, Siswa $siswa, int $x, int $y, string $fontBold, string $fontReguler, string $warnaKelas, string $warnaNama, string $warnaTeks, bool $badgeKelas): void
    {
        $img->text($siswa->nama_lengkap, $x, $y, function ($font) use ($fontBold, $warnaNama) {
            $font->file($fontBold); $font->size(28); $font->color($warnaNama); $font->valign('top');
        });

        $kelasLabel = "Kelas {$siswa->kelas}" . ($siswa->rombel ? " - {$siswa->rombel}" : '');
        $img->text($kelasLabel, $x, $y + 42, function ($font) use ($fontBold, $warnaKelas) {
            $font->file($fontBold); $font->size(18); $font->color($warnaKelas); $font->valign('top');
        });

        $baris = [
            "NIS: {$siswa->nis}",
            "NISN: {$siswa->nisn}",
            "TTL: {$siswa->tempat_lahir}, " . $siswa->tanggal_lahir->format('d/m/Y'),
        ];
        foreach ($baris as $i => $teks) {
            $img->text($teks, $x, $y + 82 + ($i * 26), function ($font) use ($fontReguler, $warnaTeks) {
                $font->file($fontReguler); $font->size(15); $font->color($warnaTeks); $font->valign('top');
            });
        }
    }
}
