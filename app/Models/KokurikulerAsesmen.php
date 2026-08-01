<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KokurikulerAsesmen extends Model
{
    protected $table = 'kokurikuler_asesmens';
    protected $fillable = ['target_dimensi_id', 'siswa_id', 'nilai_kualitatif', 'catatan_guru'];

    public function targetDimensi() { return $this->belongsTo(KokurikulerTargetDimensi::class, 'target_dimensi_id'); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}
