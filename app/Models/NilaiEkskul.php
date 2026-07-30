<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiEkskul extends Model
{
    protected $table = 'nilai_ekskuls';

    protected $fillable = [
        'siswa_id','tahun_ajaran','semester',
        'nama_ekskul','nilai_kualitatif','keterangan',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }
}
