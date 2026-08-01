<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KokurikulerTargetDimensi extends Model
{
    protected $table = 'kokurikuler_target_dimensis';
    protected $fillable = ['kegiatan_id', 'nama_dimensi'];

    public function kegiatan() { return $this->belongsTo(KokurikulerKegiatan::class, 'kegiatan_id'); }
    public function asesmens() { return $this->hasMany(KokurikulerAsesmen::class, 'target_dimensi_id'); }
}
