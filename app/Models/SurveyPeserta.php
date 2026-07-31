<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * SurveyPeserta = "Project" secara konsep di UI: satu paket survey + target
 * kelas + link publik sendiri. Nama class/tabel dipertahankan (survey_pesertas)
 * supaya tidak perlu migrasi rename besar, tapi di tampilan disebut "Project".
 */
class SurveyPeserta extends Model
{
    protected $table = 'survey_pesertas';
    protected $fillable = ['survey_id', 'target_kelas', 'token'];

    protected static function booted(): void
    {
        static::creating(function (SurveyPeserta $p) {
            if (! $p->token) {
                $p->token = Str::random(24);
            }
        });
    }

    public function survey() { return $this->belongsTo(Survey::class); }
    public function jawabans() { return $this->hasMany(SurveyJawaban::class, 'peserta_id'); }

    public function getTargetKelasArrayAttribute(): array
    {
        return $this->target_kelas ? explode(',', $this->target_kelas) : [];
    }

    /** Daftar siswa aktif yang termasuk target kelas project ini */
    public function siswaTarget()
    {
        $targets = $this->target_kelas_array;
        $query = Siswa::where('status', 'aktif');

        if (count($targets) === 0) {
            return $query->whereRaw('1 = 0');
        }

        $query->where(function ($q) use ($targets) {
            foreach ($targets as $t) {
                [$kelas, $rombel] = array_pad(explode('-', $t, 2), 2, null);
                $q->orWhere(function ($qq) use ($kelas, $rombel) {
                    $qq->where('kelas', $kelas);
                    if ($rombel) $qq->where('rombel', $rombel);
                });
            }
        });
        return $query;
    }

    public function publicUrl(): string
    {
        return url('/isi-survey/' . $this->token);
    }
}
