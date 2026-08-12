<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanKkHasil extends Model
{
    protected $table = 'scan_kk_hasil';
    protected $fillable = [
        'siswa_id', 'status_kk', 'skor_kk', 'data_kk',
        'status_akta', 'skor_akta', 'no_akta', 'data_akta',
        'pesan_error', 'discan_at', 'dikonfirmasi_at', 'dikonfirmasi_oleh_user_id',
    ];

    protected $casts = [
        'data_kk' => 'array',
        'data_akta' => 'array',
        'discan_at' => 'datetime',
        'dikonfirmasi_at' => 'datetime',
    ];

    public function sudahDikonfirmasi(): bool
    {
        return $this->dikonfirmasi_at !== null;
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function sudahDiscan(): bool
    {
        return $this->status_kk !== 'belum' || $this->status_akta !== 'belum';
    }

    /** Ambil nama_ayah/nama_ibu dari hasil OCR KK, buat dibandingkan ke data induk */
    public function namaAyahHasilScan(): ?string
    {
        return $this->data_kk['nama_ayah_siswa'] ?? null;
    }

    public function namaIbuHasilScan(): ?string
    {
        return $this->data_kk['nama_ibu_siswa'] ?? null;
    }

    /** Cari detail (nik/tgl lahir/pekerjaan) ayah atau ibu di array anggota_keluarga, dicocokkan dari nama */
    private function detailAnggota(?string $namaYangDicari): ?array
    {
        if (! $namaYangDicari) return null;
        $anggota = $this->data_kk['anggota_keluarga'] ?? [];
        foreach ($anggota as $a) {
            if (isset($a['nama_lengkap']) && strtoupper(trim($a['nama_lengkap'])) === strtoupper(trim($namaYangDicari))) {
                return $a;
            }
        }
        return null;
    }

    public function detailAyahHasilScan(): ?array
    {
        return $this->detailAnggota($this->namaAyahHasilScan());
    }

    public function detailIbuHasilScan(): ?array
    {
        return $this->detailAnggota($this->namaIbuHasilScan());
    }

    /** Detail siswa sendiri dari anggota_keluarga - ambil entri PERTAMA (sesuai urutan yg diminta di prompt: 1.Siswa 2.Ayah 3.Ibu), bukan cocokkan nama krn ejaan OCR bisa beda dari data induk kita */
    public function detailSiswaHasilScan(): ?array
    {
        return $this->data_kk['anggota_keluarga'][0] ?? null;
    }

    /** Ambil tahun dari tanggal_lahir (format bebas, cari 4 digit terakhir yg masuk akal sbg tahun) */
    public static function ekstrakTahun(?string $tanggalLahir): ?int
    {
        if (! $tanggalLahir) return null;
        if (preg_match('/(19|20)\d{2}/', $tanggalLahir, $m)) return (int) $m[0];
        return null;
    }

    /**
     * Akta sering nulis tanggal dgn kata (mis. "SEMBILAN BELAS DESEMBER DUA
     * RIBU TIGA BELAS"), sedangkan data kita/KK pakai angka (19-12-2013).
     * Fungsi ini coba baca format kata itu jadi angka "d-m-Y" biar bisa
     * dibandingkan apple-to-apple, bukan cuma cocok karakter.
     */
    public static function normalisasiTanggal(?string $teks): ?string
    {
        if (! $teks) return null;

        // Kalau sudah format angka (ada digit), pakai apa adanya
        if (preg_match('/\d/', $teks)) {
            return trim($teks);
        }

        $teks = strtoupper(trim($teks));

        $bulanMap = [
            'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
            'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
            'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12,
        ];
        $bulanPola = implode('|', array_keys($bulanMap));
        if (! preg_match('/(' . $bulanPola . ')/', $teks, $mBulan)) return null;
        $bulan = $bulanMap[$mBulan[1]];

        [$bagianTanggal, $bagianTahun] = array_pad(preg_split('/' . $mBulan[1] . '/', $teks), 2, '');

        $angkaSatuan = [
            'NOL' => 0, 'SATU' => 1, 'DUA' => 2, 'TIGA' => 3, 'EMPAT' => 4, 'LIMA' => 5,
            'ENAM' => 6, 'TUJUH' => 7, 'DELAPAN' => 8, 'SEMBILAN' => 9, 'SEPULUH' => 10,
        ];

        $kataKeAngka = function (string $frasa) use ($angkaSatuan) {
            $frasa = trim($frasa);
            if ($frasa === '') return null;

            // "X BELAS" -> 10+X (mis. SEMBILAN BELAS -> 19), "SEBELAS" -> 11
            if ($frasa === 'SEBELAS') return 11;
            if (preg_match('/^(\w+)\s+BELAS$/', $frasa, $m) && isset($angkaSatuan[$m[1]])) {
                return 10 + $angkaSatuan[$m[1]];
            }
            // "X PULUH" atau "X PULUH Y"
            if (preg_match('/^(\w+)\s+PULUH(?:\s+(\w+))?$/', $frasa, $m)) {
                $puluhan = ($angkaSatuan[$m[1]] ?? 0) * 10;
                $satuan = isset($m[2]) ? ($angkaSatuan[$m[2]] ?? 0) : 0;
                return $puluhan + $satuan;
            }
            if ($frasa === 'DUA PULUH') return 20;
            if (isset($angkaSatuan[$frasa])) return $angkaSatuan[$frasa];
            return null;
        };

        $tanggal = $kataKeAngka($bagianTanggal);

        // Tahun: format umum "DUA RIBU TIGA BELAS" / "DUA RIBU DUA PULUH SATU" dst
        $tahun = null;
        if (preg_match('/^(\w+)\s+RIBU\s*(.*)$/', trim($bagianTahun), $mTahun)) {
            $ribuan = ($angkaSatuan[$mTahun[1]] ?? 0) * 1000;
            $sisa = $kataKeAngka($mTahun[2]) ?? 0;
            $tahun = $ribuan + $sisa;
        }

        if ($tanggal === null || $tahun === null) return null;

        return sprintf('%02d-%02d-%04d', $tanggal, $bulan, $tahun);
    }
}
