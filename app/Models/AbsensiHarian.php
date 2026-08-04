<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    use BelongsToSekolah;

    protected $table = 'absensi_harians';
    protected $fillable = ['sekolah_id', 'siswa_id', 'tanggal', 'status', 'keterangan', 'dicatat_oleh_guru_id'];
    protected $casts = ['tanggal' => 'date'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function dicatatOleh() { return $this->belongsTo(Guru::class, 'dicatat_oleh_guru_id'); }
}
