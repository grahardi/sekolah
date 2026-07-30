<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $fillable = [
        'siswa_id','tahun_ajaran','semester','kelas',
        'sakit','izin','alpa',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }

    public function getTotalAttribute(): int
    {
        return $this->sakit + $this->izin + $this->alpa;
    }
}
