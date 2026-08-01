<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporDetailAkademik extends Model
{
    protected $table = 'rapor_detail_akademiks';
    protected $fillable = ['rapor_id', 'mata_pelajaran_id', 'nilai_akhir', 'nilai_katrol', 'capaian_kompetensi'];

    public function rapor() { return $this->belongsTo(Rapor::class); }
    public function mataPelajaran() { return $this->belongsTo(MataPelajaran::class); }
}
