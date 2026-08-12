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
}
