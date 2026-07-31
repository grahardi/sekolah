<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class GuruEkstrakurikuler extends Model
{
    use BelongsToSekolah;

    protected $table = 'guru_ekstrakurikulers';
    protected $fillable = ['sekolah_id', 'tahun_ajaran_id', 'pegawai_id', 'guru_id', 'nama_ekstrakurikuler'];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function pegawai() { return $this->belongsTo(Pegawai::class); }
    public function guru() { return $this->belongsTo(Guru::class); }
}
