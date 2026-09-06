<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniAjuanUlang extends Model
{
    protected $table = 'alumni_ajuan_ulang';
    protected $fillable = [
        'siswa_id', 'alumni_kategori', 'alumni_sekolah_tujuan_id', 'alumni_sekolah_tujuan_manual',
        'alumni_jurusan', 'alumni_keterangan', 'status', 'diproses_oleh_user_id', 'diproses_at',
    ];

    protected $casts = ['diproses_at' => 'datetime'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function sekolahTujuan()
    {
        return $this->belongsTo(SekolahTujuan::class, 'alumni_sekolah_tujuan_id');
    }

    public function labelTujuan(): string
    {
        return match ($this->alumni_kategori) {
            'lanjut_sekolah' => $this->sekolahTujuan?->nama_sekolah ?? $this->alumni_sekolah_tujuan_manual ?? 'Lanjut Sekolah',
            'pondok_pesantren' => $this->alumni_sekolah_tujuan_manual ? "Pondok Pesantren - {$this->alumni_sekolah_tujuan_manual}" : 'Pondok Pesantren',
            'bekerja' => $this->alumni_keterangan ? "Bekerja - {$this->alumni_keterangan}" : 'Bekerja',
            'tidak_melanjutkan' => $this->alumni_keterangan ? "Tidak Melanjutkan - {$this->alumni_keterangan}" : 'Tidak Melanjutkan',
            'lainnya' => $this->alumni_keterangan ?: 'Lainnya',
            default => $this->alumni_kategori,
        };
    }
}
