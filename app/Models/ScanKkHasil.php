<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanKkHasil extends Model
{
    protected $table = 'scan_kk_hasil';
    protected $fillable = [
        'siswa_id', 'status_kk', 'skor_kk', 'data_kk',
        'status_akta', 'skor_akta', 'no_akta', 'data_akta',
        'pesan_error', 'discan_at',
    ];

    protected $casts = [
        'data_kk' => 'array',
        'data_akta' => 'array',
        'discan_at' => 'datetime',
    ];

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
}
