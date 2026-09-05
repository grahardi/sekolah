<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class SarprasPeminjaman extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_peminjaman';
    protected $fillable = [
        'sekolah_id', 'asset_id', 'peminjam_nama', 'peminjam_kontak', 'keperluan',
        'tanggal_pinjam', 'tanggal_kembali_rencana', 'tanggal_kembali_aktual', 'status', 'catatan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(SarprasAsset::class, 'asset_id');
    }

    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'dipinjam' && $this->tanggal_kembali_rencana->isPast();
    }
}
