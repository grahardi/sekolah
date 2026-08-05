<?php

namespace App\Services;

use App\Models\Siswa;

class KartuPelajarGenerator
{
    // Ukuran CR80 (kartu ID standar) @ 300dpi: 85.6mm x 54mm
    const LEBAR = 1013;
    const TINGGI = 638;

    /** Kembalikan PNG binary siap dipakai response()/simpan file */
    public static function buat(Siswa $siswa, int $model): string
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
            self::teks($img, $fontBold, 28, $navy, self::LEBAR / 2, 64, $sekolahNama, true);
            self::teks($img, $fontReguler, 17, $abuTeks, self::LEBAR / 2, 94, 'KARTU TANDA PELAJAR', true);
            $fotoX = 44; $fotoY = 128; $fotoW = 210; $fotoH = 270;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $kuning, 5);
            $infoX = $fotoX + $fotoW + 34;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, false);
            imagefilledrectangle($img, 0, self::TINGGI - 14, self::LEBAR, self::TINGGI, $kuning);
        } elseif ($model === 3) {
            imagefilledrectangle($img, 0, 0, 22, self::TINGGI, $navy);
            self::teks($img, $fontBold, 26, $hitamTeks, 58, 44, $sekolahNama, false);
            self::teks($img, $fontReguler, 16, $abuMuda, 58, 76, 'KARTU TANDA PELAJAR', false);
            imageline($img, 58, 104, self::LEBAR - 40, 104, imagecolorallocate($img, 241, 245, 249));
            $fotoX = 58; $fotoY = 124; $fotoW = 195; $fotoH = 250;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, imagecolorallocate($img, 241, 245, 249), 4);
            $infoX = $fotoX + $fotoW + 30;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $biruTeks, $hitamTeks, imagecolorallocate($img, 100, 116, 139), false);
        } else {
            imagefilledrectangle($img, 0, 0, self::LEBAR, 84, $navy);
            self::teks($img, $fontBold, 28, $putihTeks, 40, 46, $sekolahNama, false);
            self::teks($img, $fontReguler, 16, $putihTeks, 40, 72, 'KARTU TANDA PELAJAR', false);
            $fotoX = 40; $fotoY = 112; $fotoW = 205; $fotoH = 265;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $abuBorder, 3);
            $infoX = $fotoX + $fotoW + 34;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $navy, $hitamTeks, $abuTeks, false);
            imagefilledrectangle($img, 0, self::TINGGI - 10, self::LEBAR, self::TINGGI, $navy);
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
        self::teks($img, $fontBold, 27, $warnaNama, $x, $y + 30, $siswa->nama_lengkap, $center);

        $kelasLabel = "Kelas {$siswa->kelas}" . ($siswa->rombel ? " - {$siswa->rombel}" : '');
        self::teks($img, $fontBold, 19, $warnaKelas, $x, $y + 66, $kelasLabel, $center);

        $baris = [
            "NIS: {$siswa->nis}",
            "NISN: {$siswa->nisn}",
            "TTL: {$siswa->tempat_lahir}, " . $siswa->tanggal_lahir->format('d/m/Y'),
            "Alamat: " . \Illuminate\Support\Str::limit($siswa->alamat, 40),
        ];
        foreach ($baris as $i => $t) {
            self::teks($img, $fontReguler, 16, $warnaTeks, $x, $y + 104 + ($i * 30), $t, $center);
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
