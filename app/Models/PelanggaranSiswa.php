<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class PelanggaranSiswa extends Model
{
    use BelongsToSekolah;

    protected $table = 'pelanggaran_siswas';
    protected $fillable = [
        'sekolah_id', 'siswa_id', 'dilaporkan_oleh_guru_id', 'tanggal',
        'kategori', 'poin', 'deskripsi', 'tindak_lanjut', 'status',
    ];
    protected $casts = ['tanggal' => 'date'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function pelapor() { return $this->belongsTo(Guru::class, 'dilaporkan_oleh_guru_id'); }

    /** Tingkat keparahan & poin default - sesuai referensi (bisa diedit manual) */
    public static function daftarKategori(): array
    {
        return [
            'Peringatan' => 2,
            'Ringan' => 5,
            'Sedang' => 15,
            'Berat' => 50,
        ];
    }

    public static function warnaKategori(): array
    {
        return [
            'Peringatan' => ['#bfdbfe', '#1d4ed8'],
            'Ringan' => ['#bbf7d0', '#15803d'],
            'Sedang' => ['#fde68a', '#a16207'],
            'Berat' => ['#fecaca', '#b91c1c'],
        ];
    }
}
