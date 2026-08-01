<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KokurikulerMapelTerlibat extends Model
{
    protected $table = 'kokurikuler_mapel_terlibats';
    protected $fillable = ['kegiatan_id', 'mata_pelajaran_id'];

    public function kegiatan() { return $this->belongsTo(KokurikulerKegiatan::class, 'kegiatan_id'); }
    public function mataPelajaran() { return $this->belongsTo(MataPelajaran::class); }
}
