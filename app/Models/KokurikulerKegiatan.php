<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class KokurikulerKegiatan extends Model
{
    use BelongsToSekolah;

    protected $table = 'kokurikuler_kegiatans';
    protected $fillable = ['sekolah_id', 'tahun_ajaran_id', 'nama_kegiatan', 'tema', 'deskripsi_template'];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
}
