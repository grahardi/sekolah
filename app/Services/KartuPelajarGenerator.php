<?php

namespace App\Services;

use App\Models\Siswa;

class KartuPelajarGenerator
{
    // Ukuran CR80 (kartu ID standar) @ 300dpi: 85.6mm x 54mm
    const LEBAR = 1013;
    const TINGGI = 638;

    /** Kembalikan PNG binary siap dipakai response()/simpan file */
    public static function buat(Siswa $siswa, int $model, ?string $barcodePngBinary): string
    {
        $img = imagecreatetruecolor(self::LEBAR, self::TINGGI);
        imagesavealpha($img, true);
        $putih = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, self::LEBAR, self::TINGGI, $putih);

        $navy       = imagecolorallocate($img, 30, 58, 95);
        $kuning     = imagecolorallocate($img, 251, 191, 36);
        $abuTeks    = imagecolorallocate($img, 71, 85, 105);
        $hitamTeks  = imagecolorallocate($img, 30, 41, 59);
        $putihTeks  = imagecolorallocate($img, 255, 255, 255);
        $abuMuda    = imagecolorallocate($img, 148, 163, 184);
        $biruTeks   = imagecolorallocate($img, 37, 99, 235);
        $abuBorder  = imagecolorallocate($img, 203, 213, 225);

        $fontReguler = resource_path('fonts/DejaVuSans.ttf');
        $fontBold    = resource_path('fonts/DejaVuSans-Bold.ttf');

        // Border luar tipis
        imagerectangle($img, 1, 1, self::LEBAR - 2, self::TINGGI - 2, $abuBorder);

        $sekolahNama = strtoupper($siswa->sekolah->nama ?? 'SEKOLAH');

        if ($model === 2) {
            imagefilledrectangle($img, 0, 0, self::LEBAR, 20, $kuning);
            self::teks($img, $fontBold, 26, $navy, self::LEBAR / 2, 60, $sekolahNama, true);
            self::teks($img, $fontReguler, 16, $abuTeks, self::LEBAR / 2, 88, 'KARTU TANDA PELAJAR', true);
            $fotoX = 40; $fotoY = 120; $fotoW = 190; $fotoH = 230;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $kuning, 5);
            $infoX = $fotoX + $fotoW + 30;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, false);
        } elseif ($model === 3) {
            imagefilledrectangle($img, 0, 0, 22, self::TINGGI, $navy);
            self::teks($img, $fontBold, 24, $hitamTeks, 55, 44, $sekolahNama, false);
            self::teks($img, $fontReguler, 15, $abuMuda, 55, 74, 'KARTU TANDA PELAJAR', false);
            imageline($img, 55, 100, self::LEBAR - 40, 100, imagecolorallocate($img, 241, 245, 249));
            $fotoX = 55; $fotoY = 118; $fotoW = 175; $fotoH = 210;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, imagecolorallocate($img, 241, 245, 249), 4);
            $infoX = $fotoX + $fotoW + 28;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $biruTeks, $hitamTeks, imagecolorallocate($img, 100, 116, 139), false);
        } else {
            imagefilledrectangle($img, 0, 0, self::LEBAR, 78, $navy);
            self::teks($img, $fontBold, 26, $putihTeks, 36, 44, $sekolahNama, false);
            self::teks($img, $fontReguler, 15, $putihTeks, 36, 68, 'KARTU TANDA PELAJAR', false);
            $fotoX = 36; $fotoY = 105; $fotoW = 185; $fotoH = 225;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $abuBorder, 3);
            $infoX = $fotoX + $fotoW + 30;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, false);
        }

        // Barcode di bawah (semua model)
        if ($barcodePngBinary) {
            $barcodeSrc = @imagecreatefromstring($barcodePngBinary);
            if ($barcodeSrc) {
                $bw = imagesx($barcodeSrc); $bh = imagesy($barcodeSrc);
                $targetW = self::LEBAR - 80; $targetH = 70;
                $targetX = 40; $targetY = self::TINGGI - $targetH - 34;
                imagecopyresampled($img, $barcodeSrc, $targetX, $targetY, 0, 0, $targetW, $targetH, $bw, $bh);
                imagedestroy($barcodeSrc);

                $kode = $siswa->nis ?: $siswa->nisn;
                self::teks($img, $fontBold, 16, $hitamTeks, self::LEBAR / 2, self::TINGGI - 14, $kode, true);
            }
        }

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return $data;
    }

    private static function gambarFoto($img, Siswa $siswa, int $x, int $y, int $w, int $h, $borderColor, int $borderW): void
    {
        imagefilledrectangle($img, $x - $borderW, $y - $borderW, $x + $w + $borderW, $y + $h + $borderW, $borderColor);

        $foto = null;
        try {
            $isi = @file_get_contents($siswa->foto_url);
            if ($isi) $foto = @imagecreatefromstring($isi);
        } catch (\Throwable $e) {
            $foto = null;
        }

        if ($foto) {
            $srcW = imagesx($foto); $srcH = imagesy($foto);
            // Crop tengah biar rasio pas (mirip object-fit: cover)
            $rasioTarget = $w / $h;
            $rasioSrc = $srcW / $srcH;
            if ($rasioSrc > $rasioTarget) {
                $cropH = $srcH; $cropW = (int) ($srcH * $rasioTarget);
                $cropX = (int) (($srcW - $cropW) / 2); $cropY = 0;
            } else {
                $cropW = $srcW; $cropH = (int) ($srcW / $rasioTarget);
                $cropX = 0; $cropY = (int) (($srcH - $cropH) / 2);
            }
            imagecopyresampled($img, $foto, $x, $y, $cropX, $cropY, $w, $h, $cropW, $cropH);
            imagedestroy($foto);
        } else {
            $abu = imagecolorallocate($img, 226, 232, 240);
            imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $abu);
        }
    }

    private static function tulisInfo($img, Siswa $siswa, int $x, int $y, string $fontBold, string $fontReguler, $warnaKelas, $warnaNama, $warnaTeks, bool $center): void
    {
        self::teks($img, $fontBold, 24, $warnaNama, $x, $y + 26, $siswa->nama_lengkap, $center);

        $kelasLabel = "Kelas {$siswa->kelas}" . ($siswa->rombel ? " - {$siswa->rombel}" : '');
        self::teks($img, $fontBold, 17, $warnaKelas, $x, $y + 58, $kelasLabel, $center);

        $baris = [
            "NIS: {$siswa->nis}",
            "NISN: {$siswa->nisn}",
            "TTL: {$siswa->tempat_lahir}, " . $siswa->tanggal_lahir->format('d/m/Y'),
        ];
        foreach ($baris as $i => $t) {
            self::teks($img, $fontReguler, 14, $warnaTeks, $x, $y + 92 + ($i * 24), $t, $center);
        }
    }

    /** Tulis teks dgn TTF. $y = posisi baseline atas (bukan bawah spt imagettftext bawaan) */
    private static function teks($img, string $font, int $ukuran, $warna, int $x, int $y, string $teks, bool $tengah): void
    {
        $box = imagettfbbox($ukuran, 0, $font, $teks);
        $tinggiTeks = abs($box[7] - $box[1]);
        $baselineY = $y + $tinggiTeks;

        if ($tengah) {
            $lebarTeks = abs($box[2] - $box[0]);
            $x = (int) ($x - ($lebarTeks / 2));
        }

        imagettftext($img, $ukuran, 0, $x, $baselineY, $warna, $font, $teks);
    }
}
