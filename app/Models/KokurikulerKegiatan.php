<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class KokurikulerKegiatan extends Model
{
    use BelongsToSekolah;

    protected $table = 'kokurikuler_kegiatans';
    protected $fillable = [
        'sekolah_id', 'tahun_ajaran_id', 'nama_kegiatan', 'tema', 'deskripsi_template',
        'bentuk_kegiatan', 'koordinator_guru_id', 'semester',
    ];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class); }
    public function koordinator() { return $this->belongsTo(Guru::class, 'koordinator_guru_id'); }
    public function targetDimensis() { return $this->hasMany(KokurikulerTargetDimensi::class, 'kegiatan_id'); }
    public function kelasTerlibats() { return $this->hasMany(KokurikulerKelasTerlibat::class, 'kegiatan_id'); }
    public function mapelTerlibats() { return $this->hasMany(KokurikulerMapelTerlibat::class, 'kegiatan_id'); }

    /** 8 dimensi Profil Lulusan resmi (Kurikulum Merdeka terbaru) */
    public static function daftarDimensi(): array
    {
        return [
            'Keimanan dan Ketakwaan terhadap Tuhan YME',
            'Kewargaan',
            'Penalaran Kritis',
            'Kreativitas',
            'Kolaborasi',
            'Kemandirian',
            'Kesehatan',
            'Komunikasi',
        ];
    }
}
