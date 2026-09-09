<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use BelongsToSekolah;

    protected $table = 'mata_pelajarans';
    protected $fillable = ['sekolah_id', 'nama', 'kelompok', 'is_agama', 'agama_untuk', 'urutan', 'is_non_formal'];
    protected $casts = ['is_agama' => 'boolean', 'agama_untuk' => 'array', 'is_non_formal' => 'boolean'];

    /** Mapel ini berlaku buat siswa dgn agama tsb? Non-agama selalu true (gak difilter) */
    public function cocokUntukAgama(?string $agamaSiswa): bool
    {
        if (! $this->is_agama) return true;
        if (empty($this->agama_untuk)) return true; // blm diset -> jangan sampai ke-filter semua, aman default tampil

        return in_array($agamaSiswa, $this->agama_untuk);
    }
}
