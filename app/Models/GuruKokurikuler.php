<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class GuruKokurikuler extends Model
{
    use BelongsToSekolah;

    protected $table = 'guru_kokurikulers';
    protected $fillable = ['sekolah_id', 'tahun_ajaran_id', 'pegawai_id', 'guru_id', 'tema_p5', 'kelas', 'rombel'];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function pegawai() { return $this->belongsTo(Pegawai::class); }
    public function guru() { return $this->belongsTo(Guru::class); }

    public function getKelasLengkapAttribute(): string
    {
        return $this->rombel ? "{$this->kelas} - {$this->rombel}" : $this->kelas;
    }
}
