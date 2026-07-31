<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiPegawai extends Model
{
    protected $table = 'cuti_pegawais';
    protected $fillable = ['pegawai_id', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari', 'no_surat', 'keterangan'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
}
