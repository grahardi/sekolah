<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiketDukungan extends Model
{
    protected $table = 'tiket_dukungan';
    protected $fillable = [
        'sekolah_id', 'dibuat_oleh_user_id', 'subjek', 'status', 'prioritas',
        'dibalas_terakhir_at', 'ada_balasan_belum_dibaca_sekolah', 'ada_balasan_belum_dibaca_admin',
    ];

    protected $casts = [
        'dibalas_terakhir_at' => 'datetime',
        'ada_balasan_belum_dibaca_sekolah' => 'boolean',
        'ada_balasan_belum_dibaca_admin' => 'boolean',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_user_id');
    }

    public function pesan()
    {
        return $this->hasMany(TiketPesan::class, 'tiket_id')->orderBy('created_at');
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'terbuka' => 'Terbuka',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    public function warnaBadge(): array
    {
        return match ($this->status) {
            'terbuka' => ['bg' => '#fef9c3', 'txt' => '#854d0e'],
            'diproses' => ['bg' => '#dbeafe', 'txt' => '#1e40af'],
            'selesai' => ['bg' => '#dcfce7', 'txt' => '#166534'],
            default => ['bg' => '#f1f5f9', 'txt' => '#64748b'],
        };
    }
}
