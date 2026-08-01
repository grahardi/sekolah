<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KokurikulerKelasTerlibat extends Model
{
    protected $table = 'kokurikuler_kelas_terlibats';
    protected $fillable = ['kegiatan_id', 'kelas', 'rombel'];

    public function kegiatan() { return $this->belongsTo(KokurikulerKegiatan::class, 'kegiatan_id'); }

    public function getKelasLengkapAttribute(): string
    {
        return $this->rombel ? "{$this->kelas} - {$this->rombel}" : $this->kelas;
    }
}
