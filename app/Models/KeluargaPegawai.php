<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeluargaPegawai extends Model
{
    protected $table = 'keluarga_pegawais';
    protected $fillable = ['pegawai_id', 'nama', 'hubungan', 'tanggal_lahir', 'pekerjaan', 'masih_ditanggung'];
    protected $casts = ['tanggal_lahir' => 'date', 'masih_ditanggung' => 'boolean'];

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
}
