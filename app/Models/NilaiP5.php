<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiP5 extends Model
{
    protected $table = 'nilai_p5s';

    protected $fillable = [
        'siswa_id','tahun_ajaran','semester','kelas',
        'tema_projek','topik','nilai','deskripsi',
    ];

    public function siswa() { return $this->belongsTo(Siswa::class); }

    public static function nilaiLabel(): array
    {
        return [
            'BB'  => 'Belum Berkembang',
            'MB'  => 'Mulai Berkembang',
            'BSH' => 'Berkembang Sesuai Harapan',
            'BSB' => 'Berkembang Sangat Baik',
        ];
    }
}
