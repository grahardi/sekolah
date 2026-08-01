<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpKelas extends Model
{
    protected $table = 'tp_kelas';
    protected $fillable = ['tujuan_pembelajaran_id', 'kelas', 'rombel'];

    public function tujuanPembelajaran() { return $this->belongsTo(TujuanPembelajaran::class); }
}
