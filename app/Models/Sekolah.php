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
        'uts_tanggal_manual',
        'biodata_tanggal_manual',
        'sarpras_prefix_kode',
        'sarpras_ambang_batas_pinjam_hari',
        'rapor_tampilkan_logo',
        'rapor_kota_ttd',
        'rapor_threshold_sangat_baik',
        'rapor_threshold_baik',
        'rapor_threshold_cukup',
        'logo_kabupaten',
        'logo_sekolah',
        'gemini_api_key',
        'watermark_rapor',
        'rapor_tampilkan_watermark',
        'rapor_header_custom',
        'rapor_pakai_header_custom',
        'rapor_header_custom_scale',
        'rapor_warna_tabel',
        'uts_ukuran_kertas',
        'uts_orientasi',
        'uts_font_size',
        'uts_warna_tabel',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'rapor_tampilkan_logo' => 'boolean',
        'rapor_tampilkan_watermark' => 'boolean',
        'rapor_pakai_header_custom' => 'boolean',
        'rapor_tanggal_manual' => 'date',
        'uts_tanggal_manual' => 'date',
        'biodata_tanggal_manual' => 'date',
        'gemini_api_key' => 'encrypted',
    ];

    /**
     * Key Gemini yg dipakai sekolah ini: pakai punya sendiri kalau sudah
     * diisi, kalau tidak fallback ke key default milik sekolah.co.id (.env).
     */
    public function geminiApiKeyEfektif(): ?string
    {
        return $this->gemini_api_key ?: env('GEMINI_API_KEY_DEFAULT');
    }

    public function pakaiApiKeySendiri(): bool
    {
        return filled($this->gemini_api_key);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
