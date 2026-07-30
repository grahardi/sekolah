<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiRapor extends Model
{
    protected $fillable = [
        'siswa_id','tahun_ajaran','semester','kelas',
        'mata_pelajaran','kelompok','nilai','deskripsi',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }

    public function getNilaiHurufAttribute(): string
    {
        if (is_null($this->nilai)) return '-';
        return match(true) {
            $this->nilai >= 90 => 'A',
            $this->nilai >= 80 => 'B',
            $this->nilai >= 70 => 'C',
            $this->nilai >= 60 => 'D',
            default            => 'E',
        };
    }

    /**
     * Daftar mapel SMP Kurikulum Merdeka — urutan resmi.
     * Dipakai konsisten di: input nilai, import/export Excel, PDF buku induk.
     */
    public static function daftarMapel(): array
    {
        return [
            'Pendidikan Agama dan Budi Pekerti',
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'Ilmu Pengetahuan Alam',
            'Ilmu Pengetahuan Sosial',
            'Bahasa Inggris',
            'Seni dan Budaya',
            'Pend. Jasmani, Olahraga dan Kesehatan',
            'Prakarya',
            'Informatika',
            'Bahasa Daerah',
            'Koding',
        ];
    }

    /** Backward-compat wrapper */
    public static function mapelWajibSMP(): array
    {
        return ['Mata Pelajaran' => self::daftarMapel()];
    }
}
