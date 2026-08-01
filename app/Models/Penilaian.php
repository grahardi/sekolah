<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use BelongsToSekolah;

    protected $table = 'penilaians';
    protected $fillable = [
        'sekolah_id', 'tahun_ajaran_id', 'mata_pelajaran_id', 'guru_id',
        'kelas', 'rombel', 'nama_penilaian', 'jenis_penilaian', 'subjenis_penilaian',
        'bobot_penilaian', 'semester', 'tanggal_penilaian',
    ];
    protected $casts = ['tanggal_penilaian' => 'date'];

    public function mataPelajaran() { return $this->belongsTo(MataPelajaran::class); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function tujuanPembelajarans() { return $this->belongsToMany(TujuanPembelajaran::class, 'penilaian_tp', 'penilaian_id', 'tujuan_pembelajaran_id'); }
    public function nilais() { return $this->hasMany(PenilaianDetailNilai::class); }

    public function getKelasLengkapAttribute(): string
    {
        return $this->rombel ? "{$this->kelas} - {$this->rombel}" : $this->kelas;
    }

    public function getSubjenisLabelAttribute(): string
    {
        return match ($this->subjenis_penilaian) {
            'Sumatif TP' => 'Sumatif - TP',
            'Sumatif Tengah Semester' => 'Penilaian Tengah Semester',
            'Sumatif Akhir Semester' => 'Penilaian Semester Akhir',
            default => $this->subjenis_penilaian ?? $this->jenis_penilaian,
        };
    }
}
