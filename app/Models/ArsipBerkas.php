<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArsipBerkas extends Model
{
    protected $table = 'arsip_berkas';

    protected $fillable = [
        'siswa_id',
        'foto','kartu_keluarga','akta_lahir','ijazah_sd',
        'ijazah','transkrip_nilai','sertifikat_tka',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public static function berkasAktif(): array
    {
        return [
            'foto'           => ['label' => 'Foto Siswa',     'icon' => 'ti-photo'],
            'kartu_keluarga' => ['label' => 'Kartu Keluarga', 'icon' => 'ti-id'],
            'akta_lahir'     => ['label' => 'Akta Lahir',     'icon' => 'ti-certificate'],
            'ijazah_sd'      => ['label' => 'Ijazah SD/MI',   'icon' => 'ti-school'],
        ];
    }

    public static function berkasLulus(): array
    {
        return [
            'ijazah'          => ['label' => 'Ijazah SMP',      'icon' => 'ti-certificate-2'],
            'transkrip_nilai' => ['label' => 'Transkrip Nilai',  'icon' => 'ti-report'],
            'sertifikat_tka'  => ['label' => 'Sertifikat TKA',  'icon' => 'ti-rosette'],
        ];
    }

    public function isImage(string $field): bool
    {
        $path = $this->$field;
        if (!$path) return false;
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']);
    }

    public function getUrl(string $field): ?string
    {
        return $this->$field ? Storage::disk('public')->url($this->$field) : null;
    }
}
