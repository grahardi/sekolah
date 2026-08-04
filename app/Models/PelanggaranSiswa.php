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

    /** Kategori & poin standar - bisa dipilih cepat saat lapor */
    public static function daftarKategori(): array
    {
        return [
            'Terlambat' => 5,
            'Atribut Tidak Lengkap' => 5,
            'Tidak Mengerjakan Tugas' => 10,
            'Bolos Pelajaran' => 15,
            'Merokok' => 25,
            'Berkelahi' => 50,
            'Membawa Barang Terlarang' => 75,
            'Lainnya' => 10,
        ];
    }
}
