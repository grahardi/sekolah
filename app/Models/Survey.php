<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Survey extends Model
{
    protected $fillable = ['sekolah_id', 'user_id', 'judul', 'deskripsi', 'jenis', 'token', 'target_kelas', 'status'];

    protected static function booted(): void
    {
        static::addGlobalScope('sekolah', function ($query) {
            if (Auth::check() && Auth::user()->sekolah_id) {
                $query->where('surveys.sekolah_id', Auth::user()->sekolah_id);
            }
        });

        static::creating(function (Survey $survey) {
            if (! $survey->sekolah_id && Auth::check()) {
                $survey->sekolah_id = Auth::user()->sekolah_id;
            }
            if (! $survey->user_id && Auth::check()) {
                $survey->user_id = Auth::id();
            }
            if (! $survey->token) {
                $survey->token = Str::random(24);
            }
        });
    }

    public function pertanyaans() { return $this->hasMany(SurveyPertanyaan::class)->orderBy('urutan'); }
    public function jawabans() { return $this->hasMany(SurveyJawaban::class); }
    public function pembuat() { return $this->belongsTo(User::class, 'user_id'); }

    /** Daftar kelas-rombel target sebagai array, kosong = semua kelas */
    public function getTargetKelasArrayAttribute(): array
    {
        return $this->target_kelas ? explode(',', $this->target_kelas) : [];
    }

    /** Query siswa yang jadi target survey ini (sesuai kelas-rombel terpilih) */
    public function siswaTarget()
    {
        $query = Siswa::where('status', 'aktif');
        $targets = $this->target_kelas_array;

        if (count($targets) > 0) {
            $query->where(function ($q) use ($targets) {
                foreach ($targets as $t) {
                    [$kelas, $rombel] = array_pad(explode('-', $t, 2), 2, null);
                    $q->orWhere(function ($qq) use ($kelas, $rombel) {
                        $qq->where('kelas', $kelas);
                        if ($rombel) $qq->where('rombel', $rombel);
                    });
                }
            });
        }
        return $query;
    }

    public function publicUrl(): string
    {
        return url('/survey/' . $this->token);
    }
}
