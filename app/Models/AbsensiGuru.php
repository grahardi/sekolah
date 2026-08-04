<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    use BelongsToSekolah;

    protected $table = 'absensi_gurus';
    protected $fillable = ['sekolah_id', 'guru_id', 'tanggal', 'status', 'keterangan', 'dicatat_oleh_guru_id'];
    protected $casts = ['tanggal' => 'date'];

    public function guru() { return $this->belongsTo(Guru::class, 'guru_id'); }
    public function dicatatOleh() { return $this->belongsTo(Guru::class, 'dicatat_oleh_guru_id'); }
}
