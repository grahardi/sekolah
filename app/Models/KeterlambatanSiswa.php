<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class KeterlambatanSiswa extends Model
{
    use BelongsToSekolah;

    protected $table = 'keterlambatan_siswas';
    protected $fillable = ['sekolah_id', 'siswa_id', 'tanggal', 'keterangan', 'dicatat_oleh_guru_id'];
    protected $casts = ['tanggal' => 'date'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
}
