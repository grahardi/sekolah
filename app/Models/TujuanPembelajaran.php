<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class TujuanPembelajaran extends Model
{
    use BelongsToSekolah;

    protected $table = 'tujuan_pembelajarans';
    protected $fillable = ['sekolah_id', 'mata_pelajaran_id', 'guru_id', 'tahun_ajaran_id', 'fase', 'kode_tp', 'deskripsi_tp', 'semester'];

    public function mataPelajaran() { return $this->belongsTo(MataPelajaran::class); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function kelasList() { return $this->hasMany(TpKelas::class, 'tujuan_pembelajaran_id'); }
    public function penilaians() { return $this->belongsToMany(Penilaian::class, 'penilaian_tp', 'tujuan_pembelajaran_id', 'penilaian_id'); }

    public function getKelasArrayAttribute(): array
    {
        return $this->kelasList->map(fn ($k) => $k->rombel ? "{$k->kelas}-{$k->rombel}" : $k->kelas)->toArray();
    }
}
