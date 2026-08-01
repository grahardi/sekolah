<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianDetailNilai extends Model
{
    protected $table = 'penilaian_detail_nilais';
    protected $fillable = ['penilaian_id', 'siswa_id', 'nilai'];

    public function penilaian() { return $this->belongsTo(Penilaian::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}
