<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use BelongsToSekolah;

    protected $table = 'mata_pelajarans';
    protected $fillable = ['sekolah_id', 'nama', 'kelompok'];
}
