<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sekolah_id',
        'nisn','nis','nama_lengkap','kode_akses','jenis_kelamin','tempat_lahir','tanggal_lahir',
        'agama','nik','no_kk',
        'alamat','rt','rw','dusun','kelurahan','kecamatan','kode_pos',
        'lintang','bujur',
        'no_telepon','email',
        'kelas','diterima_di_kelas','rombel','tahun_masuk','status','tanggal_diterima',
        'no_sttb_sd','asal_sekolah','no_un_sd',
        'anak_ke',
        'golongan_darah','tinggi_badan','berat_badan','riwayat_penyakit',
        'nama_ayah','nik_ayah','tahun_lahir_ayah','pendidikan_ayah','pekerjaan_ayah','penghasilan_ayah',
        'nama_ibu','nik_ibu','tahun_lahir_ibu','pendidikan_ibu','pekerjaan_ibu','penghasilan_ibu',
        'nama_wali','pekerjaan_wali','no_telepon_ortu','alamat_ortu',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir'    => 'date',
        'tanggal_diterima' => 'date',
        'lintang'          => 'decimal:7',
        'bujur'            => 'decimal:7',
    ];

    /**
     * Isolasi data antar sekolah (multi-tenant): setiap sekolah cuma bisa
     * melihat & membuat data siswanya sendiri. Ditaruh di sini (bukan di
     * tiap controller satu-satu) supaya konsisten otomatis di semua query,
     * termasuk yang mungkin lupa ditambahkan manual nanti.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('sekolah', function ($query) {
            if (Auth::check() && Auth::user()->sekolah_id) {
                $query->where('siswas.sekolah_id', Auth::user()->sekolah_id);
            }
        });

        static::creating(function (Siswa $siswa) {
            if (! $siswa->sekolah_id && Auth::check()) {
                $siswa->sekolah_id = Auth::user()->sekolah_id;
            }
        });
    }

    public function sekolah() { return $this->belongsTo(Sekolah::class); }
    public function nilaiRapors()   { return $this->hasMany(NilaiRapor::class); }
    public function nilaiP5s()      { return $this->hasMany(NilaiP5::class); }
    public function nilaiEkskuls()  { return $this->hasMany(NilaiEkskul::class); }
    public function kehadirans()    { return $this->hasMany(Kehadiran::class); }
    public function riwayatKelas()  { return $this->hasMany(RiwayatKelas::class)->orderBy('tahun_ajaran'); }
    public function arsipBerkas()   { return $this->hasOne(ArsipBerkas::class); }
    public function scanKkHasil()   { return $this->hasOne(\App\Models\ScanKkHasil::class); }
    public function pengajuanPerubahan() { return $this->hasOne(\App\Models\PengajuanPerubahan::class); }
    public function prestasis()     { return $this->hasMany(Prestasi::class)->orderByDesc('tanggal_kegiatan'); }

    public function getRombelLengkapAttribute(): string
    {
        if ($this->rombel) {
            return "{$this->kelas} - {$this->rombel}";
        }
        return (string) $this->kelas;
    }

    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : 0;
    }

    public function getJenisKelaminLengkapAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /** Kode acak dipakai bareng NISN utk link QR - dibuat sekali kalau belum ada */
    public function getOrCreateKodeAkses(): string
    {
        if (! $this->kode_akses) {
            $this->kode_akses = \Illuminate\Support\Str::random(10);
            $this->saveQuietly();
        }
        return $this->kode_akses;
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        // Kalau foto profil siswa kosong, coba pakai foto yg diupload lewat
        // Berkas (arsip_berkas.foto) - biar tidak perlu upload dobel.
        if ($this->arsipBerkas && $this->arsipBerkas->foto) {
            return asset('storage/' . $this->arsipBerkas->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap)
             . '&background=dbeafe&color=1d4ed8&size=128';
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt   ? 'RT ' . $this->rt   : null,
            $this->rw   ? 'RW ' . $this->rw   : null,
            $this->dusun,
            $this->kelurahan,
            $this->kecamatan,
            $this->kode_pos,
        ]);
        return implode(', ', $parts);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $s) {
            $q->where(function ($q) use ($s) {
                $q->where('nama_lengkap','ilike',"%{$s}%")
                  ->orWhere('nisn','ilike',"%{$s}%")
                  ->orWhere('nis','ilike',"%{$s}%");
            });
        });
        $query->when($filters['kelas_rombel'] ?? null, function ($q, $v) {
            [$kelas, $rombel] = array_pad(explode('|', $v), 2, null);
            $q->where('kelas', $kelas)->where('rombel', $rombel ?: null);
        });
        $query->when($filters['status']        ?? null, fn($q,$v) => $q->where('status',$v));
        $query->when($filters['tingkat']       ?? null, fn($q,$v) => $q->where('kelas',$v));
        $query->when($filters['tahun_masuk']   ?? null, fn($q,$v) => $q->where('tahun_masuk',$v));
    }

    public function nilaiRaporGrouped(): array
    {
        $result = [];
        foreach ($this->nilaiRapors()->orderBy('tahun_ajaran')->orderBy('semester')->get() as $n) {
            $result[$n->tahun_ajaran][$n->semester][$n->mata_pelajaran] = $n;
        }
        return $result;
    }
}
