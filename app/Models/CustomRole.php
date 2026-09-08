<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class CustomRole extends Model
{
    use BelongsToSekolah;

    protected $table = 'custom_roles';
    protected $fillable = ['sekolah_id', 'nama'];

    public const DAFTAR_MODUL = [
        'data-siswa' => 'Data Siswa (Buku Induk)',
        'alumni' => 'Alumni',
        'erapor' => 'E-Rapor',
        'sarpras' => 'Sarpras',
        'kepegawaian' => 'Kepegawaian',
        'manajemen-sekolah' => 'Manajemen Sekolah',
        'ujian' => 'Program Ujian (Server Ujian)',
        'pengajuan-perubahan' => 'Ajuan Perubahan Data',
    ];

    public function permissions()
    {
        return $this->hasMany(CustomRolePermission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissionMap(): array
    {
        $tersimpan = $this->permissions->keyBy('modul_key');

        $map = [];
        foreach (self::DAFTAR_MODUL as $key => $label) {
            $p = $tersimpan->get($key);
            $map[$key] = [
                'boleh_akses' => $p?->boleh_akses ?? false,
                'read_only' => $p?->read_only ?? true,
            ];
        }
        return $map;
    }
}
