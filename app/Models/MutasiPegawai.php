<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiPegawai extends Model
{
    protected $table = 'mutasi_pegawais';
    protected $fillable = ['pegawai_id', 'jenis_mutasi', 'tanggal_mutasi', 'asal', 'tujuan', 'no_sk', 'keterangan'];
    protected $casts = ['tanggal_mutasi' => 'date'];

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
}
