<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use BelongsToSekolah;

    protected $table = 'gurus';
    protected $fillable = ['sekolah_id', 'pegawai_id', 'user_id', 'nama', 'nip_nuptk', 'keterangan'];

    public function pegawai() { return $this->belongsTo(Pegawai::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function isDariKepegawaian(): bool
    {
        return ! is_null($this->pegawai_id);
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->pegawai && $this->pegawai->foto) {
            return asset('storage/' . $this->pegawai->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama)
             . '&background=1E3A5F&color=fff&size=128&bold=true';
    }

    /**
     * Sinkronkan semua Pegawai sekolah ini jadi entri Guru (kalau belum ada),
     * supaya semua staff Kepegawaian otomatis muncul sbg calon guru tanpa
     * perlu didaftarkan manual dua kali.
     */
    public static function syncFromPegawai(int $sekolahId): void
    {
        $sudahAda = static::withoutGlobalScopes()
            ->where('sekolah_id', $sekolahId)
            ->whereNotNull('pegawai_id')
            ->pluck('pegawai_id');

        $pegawaiBaru = Pegawai::withoutGlobalScopes()
            ->where('sekolah_id', $sekolahId)
            ->whereNotIn('id', $sudahAda)
            ->get();

        foreach ($pegawaiBaru as $p) {
            static::create([
                'sekolah_id' => $sekolahId,
                'pegawai_id' => $p->id,
                'nama' => $p->nama_lengkap,
                'nip_nuptk' => $p->nip_nuptk,
            ]);
        }
    }

    /** Cari Guru yg terhubung ke Pegawai ini, buat kalau belum ada */
    public static function findOrCreateForPegawai(Pegawai $pegawai): self
    {
        return static::firstOrCreate(
            ['pegawai_id' => $pegawai->id],
            [
                'sekolah_id' => $pegawai->sekolah_id,
                'nama' => $pegawai->nama_lengkap,
                'nip_nuptk' => $pegawai->nip_nuptk,
            ]
        );
    }
}
