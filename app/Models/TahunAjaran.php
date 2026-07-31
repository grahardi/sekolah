<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use BelongsToSekolah;

    protected $table = 'tahun_ajarans';
    protected $fillable = ['sekolah_id', 'nama', 'semester', 'is_aktif'];
    protected $casts = ['is_aktif' => 'boolean'];

    public function getLabelAttribute(): string
    {
        return "{$this->nama} - Semester {$this->semester}";
    }
}
