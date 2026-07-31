<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanPegawai extends Model
{
    protected $table = 'riwayat_pendidikan_pegawais';
    protected $fillable = ['pegawai_id', 'jenjang', 'nama_institusi', 'jurusan', 'tahun_lulus', 'no_ijazah'];

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
}
