<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    use BelongsToSekolah;

    protected $table = 'rapors';
    protected $fillable = [
        'sekolah_id', 'siswa_id', 'tahun_ajaran_id', 'kelas', 'rombel', 'semester',
        'sakit', 'izin', 'tanpa_keterangan', 'catatan_wali_kelas',
        'deskripsi_kokurikuler', 'deskripsi_ekstrakurikuler', 'status', 'tanggal_rapor',
    ];
    protected $casts = ['tanggal_rapor' => 'date'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function detailAkademik() { return $this->hasMany(RaporDetailAkademik::class); }
    public function detailEkskul() { return $this->hasMany(RaporDetailEkskul::class); }

    public function getKelasLengkapAttribute(): string
    {
        return $this->rombel ? "{$this->kelas} - {$this->rombel}" : $this->kelas;
    }
}
