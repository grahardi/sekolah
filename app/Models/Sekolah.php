<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'npsn',
        'nama',
        'is_demo',
        'alamat',
        'telepon',
        'email',
        'website',
        'kepala_sekolah_nama',
        'kepala_sekolah_nip',
        'kepala_sekolah_pangkat',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'status_sekolah',
        'bentuk_pendidikan',
        'jenjang_pendidikan',
        'kkm',
        'rapor_ukuran_kertas',
        'rapor_orientasi',
        'rapor_font_size',
        'rapor_tanggal_manual',
        'rapor_tampilkan_logo',
        'rapor_kota_ttd',
        'rapor_threshold_sangat_baik',
        'rapor_threshold_baik',
        'rapor_threshold_cukup',
        'logo_kabupaten',
        'logo_sekolah',
        'watermark_rapor',
        'rapor_tampilkan_watermark',
        'rapor_header_custom',
        'rapor_pakai_header_custom',
        'rapor_warna_tabel',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'rapor_tampilkan_logo' => 'boolean',
        'rapor_tampilkan_watermark' => 'boolean',
        'rapor_pakai_header_custom' => 'boolean',
        'rapor_tanggal_manual' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
