<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';

    protected $fillable = [
        'siswa_id','tahun_ajaran','kelas','rombel',
        'wali_kelas','hasil','catatan',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }
}
