<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontenHalaman extends Model
{
    protected $table = 'konten_halaman';
    protected $fillable = ['halaman', 'kunci', 'nilai'];

    public static function ambilSemua(string $halaman = 'welcome'): array
    {
        return static::where('halaman', $halaman)->pluck('nilai', 'kunci')->toArray();
    }

    public static function simpan(string $halaman, array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            static::updateOrCreate(['kunci' => $kunci], ['halaman' => $halaman, 'nilai' => $nilai]);
        }
    }
}
