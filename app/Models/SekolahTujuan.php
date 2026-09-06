<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class SekolahTujuan extends Model
{
    use BelongsToSekolah;

    protected $table = 'sekolah_tujuan';
    protected $fillable = ['sekolah_id', 'nama_sekolah', 'jenjang', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
}
