<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class AjuanBk extends Model
{
    use BelongsToSekolah;

    protected $table = 'ajuan_bk';
    protected $fillable = ['sekolah_id', 'siswa_id', 'diajukan_oleh_guru_id', 'alasan', 'status'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function pengaju() { return $this->belongsTo(Guru::class, 'diajukan_oleh_guru_id'); }
}
