<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengajuanPerubahan extends Model
{
    protected $table = 'pengajuan_perubahan';
    protected $fillable = [
        'siswa_id', 'token', 'status', 'data_perubahan', 'catatan_siswa',
        'diajukan_at', 'diproses_oleh_user_id', 'diproses_at',
    ];

    protected $casts = [
        'data_perubahan' => 'array',
        'diajukan_at' => 'datetime',
        'diproses_at' => 'datetime',
    ];

    // Field yg boleh diajukan perubahannya oleh siswa/wali - HANYA data pokok
    // (bukan kelas/status/nilai/dll yg sensitif thd sistem akademik)
    public const FIELD_BOLEH_DIAJUKAN = [
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'agama',
        'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
        'no_telepon', 'email',
        'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
        'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
        'nama_wali', 'pekerjaan_wali', 'no_telepon_ortu', 'alamat_ortu',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh_user_id');
    }

    public static function buatAtauAmbilUntuk(Siswa $siswa): self
    {
        return static::firstOrCreate(
            ['siswa_id' => $siswa->id],
            ['status' => 'belum_isi']
        );
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'belum_isi' => 'Belum Mengisi',
            'menunggu_approval' => 'Menunggu Approval',
            'sudah_approve' => 'Sudah Approve',
            default => $this->status,
        };
    }

    public function warnaBadge(): array
    {
        return match ($this->status) {
            'belum_isi' => ['bg' => '#f1f5f9', 'txt' => '#64748b'],
            'menunggu_approval' => ['bg' => '#fef9c3', 'txt' => '#854d0e'],
            'sudah_approve' => ['bg' => '#dcfce7', 'txt' => '#166534'],
            default => ['bg' => '#f1f5f9', 'txt' => '#64748b'],
        };
    }
}
