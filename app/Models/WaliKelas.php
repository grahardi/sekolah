<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class WaliKelas extends Model
{
    use BelongsToSekolah;

    protected $table = 'wali_kelas';
    protected $fillable = ['sekolah_id', 'tahun_ajaran_id', 'pegawai_id', 'kelas', 'rombel'];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function pegawai() { return $this->belongsTo(Pegawai::class); }

    public function getKelasLengkapAttribute(): string
    {
        return $this->rombel ? "{$this->kelas} - {$this->rombel}" : $this->kelas;
    }
}
