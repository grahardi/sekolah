<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Prestasi extends Model
{
    protected $table = 'prestasis';

    protected $fillable = [
        'siswa_id','tanggal_kegiatan','jenis_lomba',
        'tingkat_lomba','juara','penyelenggara',
        'keterangan','sertifikat',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function getSertifikatUrlAttribute(): ?string
    {
        return $this->sertifikat ? Storage::disk('public')->url($this->sertifikat) : null;
    }

    public static function tingkatList(): array
    {
        return ['Sekolah','Kecamatan','Kabupaten/Kota','Provinsi','Nasional','Internasional'];
    }
}
