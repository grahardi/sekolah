<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class NotifikasiWaliKelas extends Model
{
    use BelongsToSekolah;

    protected $table = 'notifikasi_wali_kelas';
    protected $fillable = ['sekolah_id', 'siswa_id', 'dari_guru_id', 'pesan', 'sudah_dibaca'];
    protected $casts = ['sudah_dibaca' => 'boolean'];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function dariGuru() { return $this->belongsTo(Guru::class, 'dari_guru_id'); }
}
