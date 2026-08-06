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

        $biru       = imagecolorallocate($img, 30, 64, 130);   // biru kartu 1
        $hijau      = imagecolorallocate($img, 22, 101, 52);   // hijau kartu 2
        $emas       = imagecolorallocate($img, 202, 138, 4);   // aksen emas kartu 2
        $biruMuda   = imagecolorallocate($img, 219, 234, 254); // header kartu 3
        $biruTua3   = imagecolorallocate($img, 30, 58, 138);   // subtitle bar kartu 3
        $abuTeks    = imagecolorallocate($img, 71, 85, 105);
        $hitamTeks  = imagecolorallocate($img, 30, 41, 59);
        $putihTeks  = imagecolorallocate($img, 255, 255, 255);
        $abuBorder  = imagecolorallocate($img, 203, 213, 225);

        $fontReguler = resource_path('fonts/DejaVuSans.ttf');
        $fontBold    = resource_path('fonts/DejaVuSans-Bold.ttf');

        // Border luar tipis
        imagerectangle($img, 1, 1, self::LEBAR - 2, self::TINGGI - 2, $abuBorder);

        $sekolahNama = strtoupper($siswa->sekolah->nama ?? 'SEKOLAH');
        $alamatSekolah = $siswa->sekolah->alamat ?? '';
        $berlakuSampai = '30 Juni ' . (now()->month <= 6 ? now()->year : now()->year + 1);

        if ($model === 2) {
            // ── Model 2: Hijau, aksen emas ──
            imagefilledrectangle($img, 0, 0, self::LEBAR, 118, $hijau);
            imagefilledrectangle($img, 0, 118, self::LEBAR, 124, $emas);
            self::gambarAksenBackground($img, $hijau, 124);
            self::gambarLogoBulat($img, $siswa, 60, 60, 44, $putih, $hijau, $fontBold);
            self::teks($img, $fontBold, 30, $putihTeks, 122, 30, 'KARTU PELAJAR', false);
            self::teks($img, $fontBold, 24, imagecolorallocate($img, 253, 224, 71), 122, 66, $sekolahNama, false);

            $fotoX = 44; $fotoY = 156; $fotoW = 195; $fotoH = 250;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $emas, 5);
            $infoX = $fotoX + $fotoW + 34;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $hijau, $hitamTeks, $abuTeks, $berlakuSampai);
            imagefilledrectangle($img, 0, self::TINGGI - 12, self::LEBAR, self::TINGGI, $emas);
        } elseif ($model === 3) {
            // ── Model 3: Header biru muda, subtitle bar biru tua, foto di kanan ──
            imagefilledrectangle($img, 0, 0, self::LEBAR, 96, $biruMuda);
            imagefilledrectangle($img, 0, 96, self::LEBAR, 132, $biruTua3);
            self::gambarAksenBackground($img, $biruMuda, 132);
            self::gambarLogoBulat($img, $siswa, 56, 48, 40, $biru, $putih, $fontBold);
            self::teks($img, $fontBold, 24, $hitamTeks, 112, 16, $sekolahNama, false);
            self::teks($img, $fontReguler, 15, $abuTeks, 112, 46, $alamatSekolah ?: 'Kartu Identitas Pelajar', false);
            self::teks($img, $fontBold, 18, $putihTeks, self::LEBAR / 2, 108, 'KARTU IDENTITAS PELAJAR', true);

            $fotoW = 195; $fotoH = 250; $fotoY = 162;
            $fotoX = self::LEBAR - $fotoW - 44; // foto di KANAN utk model ini
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $biruTua3, 4);
            $infoX = 48;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $biru, $hitamTeks, $abuTeks, $berlakuSampai);
        } else {
            // ── Model 1 (default): Biru solid ──
            imagefilledrectangle($img, 0, 0, self::LEBAR, 118, $biru);
            self::gambarAksenBackground($img, $biru, 118);
            self::gambarLogoBulat($img, $siswa, 60, 59, 44, $putih, $biru, $fontBold);
            self::teks($img, $fontBold, 30, $putihTeks, 122, 26, 'KARTU PELAJAR', false);
            self::teks($img, $fontBold, 24, $putihTeks, 122, 62, $sekolahNama, false);
            if ($alamatSekolah) self::teks($img, $fontReguler, 14, imagecolorallocate($img, 191, 219, 254), 122, 92, $alamatSekolah, false);

            $fotoX = 44; $fotoY = 152; $fotoW = 195; $fotoH = 250;
            self::gambarFoto($img, $siswa, $fotoX, $fotoY, $fotoW, $fotoH, $abuBorder, 3);
            $infoX = $fotoX + $fotoW + 34;
            self::tulisInfo($img, $siswa, $infoX, $fotoY, $fontBold, $fontReguler, $biru, $hitamTeks, $abuTeks, $berlakuSampai);
            imagefilledrectangle($img, 0, self::TINGGI - 10, self::LEBAR, self::TINGGI, $biru);
        }

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return $data;
    }

    /** Lingkaran logo - pakai logo sekolah yg diupload, atau Tut Wuri Handayani sbg default */
    private static function gambarLogoBulat($img, Siswa $siswa, int $cx, int $cy, int $r, $warnaLingkaran, $warnaTeks, string $fontBold): void
    {
        imagefilledellipse($img, $cx, $cy, $r * 2 + 6, $r * 2 + 6, $warnaLingkaran);

        $logoPath = null;
        $logoSekolah = $siswa->sekolah->logo_sekolah ?? null;
        if ($logoSekolah && file_exists(storage_path('app/public/' . $logoSekolah))) {
            $logoPath = storage_path('app/public/' . $logoSekolah);
        } else {
            $fallback = resource_path('seed-assets/tutwuri.png');
            if (file_exists($fallback)) $logoPath = $fallback;
        }

        if (! $logoPath) return;

        $logo = @imagecreatefromstring(@file_get_contents($logoPath));
        if (! $logo) return;

        $srcW = imagesx($logo); $srcH = imagesy($logo);
        $ukuran = (int) ($r * 1.7);
        imagealphablending($img, true);
        imagecopyresampled($img, $logo, $cx - (int)($ukuran / 2), $cy - (int)($ukuran / 2), 0, 0, $ukuran, $ukuran, $srcW, $srcH);
        imagedestroy($logo);
    }

    /** Aksen dekoratif tipis di background body kartu - biar tidak polos kosong */
    private static function gambarAksenBackground($img, $warnaAksen, int $mulaiY): void
    {
        $warnaTransparan = imagecolorallocatealpha($img, imagecolorsforindex($img, $warnaAksen)['red'], imagecolorsforindex($img, $warnaAksen)['green'], imagecolorsforindex($img, $warnaAksen)['blue'], 110);
        imagealphablending($img, true);
        // Lingkaran besar transparan pojok kanan bawah sbg aksen
        imagefilledellipse($img, self::LEBAR - 40, self::TINGGI - 20, 260, 260, $warnaTransparan);
        imagefilledellipse($img, self::LEBAR - 100, self::TINGGI + 40, 180, 180, $warnaTransparan);
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

    private static function tulisInfo($img, Siswa $siswa, int $x, int $y, string $fontBold, string $fontReguler, $warnaAksen, $warnaNama, $warnaTeks, string $berlakuSampai): void
    {
        self::teks($img, $fontBold, 26, $warnaNama, $x, $y + 4, $siswa->nama_lengkap, false);

        $baris = [
            'NIS/NISN' => "{$siswa->nis} / {$siswa->nisn}",
            'TTL' => "{$siswa->tempat_lahir}, " . $siswa->tanggal_lahir->format('d/m/Y'),
            'Alamat' => \Illuminate\Support\Str::limit($siswa->alamat, 36),
            'Kelas' => "{$siswa->kelas}" . ($siswa->rombel ? " - {$siswa->rombel}" : ''),
        ];
        $baseY = $y + 46;
        $i = 0;
        foreach ($baris as $label => $isi) {
            self::teks($img, $fontBold, 15, $warnaTeks, $x, $baseY + ($i * 32), $label, false);
            self::teks($img, $fontReguler, 15, $warnaNama, $x + 150, $baseY + ($i * 32), ": {$isi}", false);
            $i++;
        }

        self::teks($img, $fontBold, 15, $warnaAksen, $x, $baseY + ($i * 32) + 10, 'Berlaku Sampai', false);
        self::teks($img, $fontBold, 15, $warnaAksen, $x + 150, $baseY + ($i * 32) + 10, ": {$berlakuSampai}", false);
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
