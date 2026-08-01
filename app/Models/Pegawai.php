<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sekolah_id',
        'nip_nuptk', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kepegawaian', 'jabatan', 'unit_kerja',
        'golongan', 'pangkat', 'tmt_cpns', 'tmt_pns', 'no_sk_pangkat',
        'tmt_pangkat_terakhir', 'tmt_gaji_berkala_terakhir',
        'pendidikan_terakhir', 'no_hp', 'email', 'alamat', 'foto',
        'status_aktif', 'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'tmt_pangkat_terakhir' => 'date',
        'tmt_gaji_berkala_terakhir' => 'date',
        'tanggal_masuk' => 'date',
    ];

    public function riwayatPendidikan() { return $this->hasMany(RiwayatPendidikanPegawai::class); }
    public function keluarga() { return $this->hasMany(KeluargaPegawai::class); }
    public function cuti() { return $this->hasMany(CutiPegawai::class); }
    public function mutasi() { return $this->hasMany(MutasiPegawai::class); }

    /** Status yang tergolong ASN - field golongan/pangkat/TMT hanya relevan untuk ini */
    public const STATUS_ASN = ['PNS', 'PPPK'];

    protected static function booted(): void
    {
        static::addGlobalScope('sekolah', function ($query) {
            if (Auth::check() && Auth::user()->sekolah_id) {
                $query->where('pegawais.sekolah_id', Auth::user()->sekolah_id);
            }
        });

        static::creating(function (Pegawai $pegawai) {
            if (! $pegawai->sekolah_id && Auth::check()) {
                $pegawai->sekolah_id = Auth::user()->sekolah_id;
            }
        });
    }

    public function isAsn(): bool
    {
        return in_array($this->jenis_kepegawaian, self::STATUS_ASN);
    }

    /** Kenaikan pangkat reguler PNS/PPPK setiap 4 tahun dari TMT pangkat terakhir */
    public function getJatuhTempoPangkatAttribute(): ?\Carbon\Carbon
    {
        if (! $this->isAsn() || ! $this->tmt_pangkat_terakhir) {
            return null;
        }
        return $this->tmt_pangkat_terakhir->copy()->addYears(4);
    }

    /** Kenaikan Gaji Berkala (KGB) setiap 2 tahun dari TMT terakhir */
    public function getJatuhTempoGajiBerkalaAttribute(): ?\Carbon\Carbon
    {
        if (! $this->isAsn() || ! $this->tmt_gaji_berkala_terakhir) {
            return null;
        }
        return $this->tmt_gaji_berkala_terakhir->copy()->addYears(2);
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap)
             . '&background=eff6ff&color=2563EB&size=128';
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(fn ($qq) => $qq
                ->where('nama_lengkap', 'ilike', "%{$v}%")
                ->orWhere('nip_nuptk', 'ilike', "%{$v}%")
                ->orWhere('jabatan', 'ilike', "%{$v}%")))
            ->when($filters['status_aktif'] ?? null, fn ($q, $v) => $q->where('status_aktif', $v))
            ->when($filters['jenis_kepegawaian'] ?? null, fn ($q, $v) => $q->where('jenis_kepegawaian', $v))
            ->when($filters['unit_kerja'] ?? null, fn ($q, $v) => $q->where('unit_kerja', $v));
    }
}
