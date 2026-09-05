<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class SarprasRiwayatKerusakan extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_riwayat_kerusakan';
    protected $fillable = [
        'sekolah_id', 'asset_id', 'tanggal_lapor', 'deskripsi_kerusakan',
        'status', 'tanggal_selesai', 'biaya_perbaikan', 'catatan',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
        'tanggal_selesai' => 'date',
        'biaya_perbaikan' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(SarprasAsset::class, 'asset_id');
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'dilaporkan' => 'Dilaporkan',
            'diperbaiki' => 'Sudah Diperbaiki',
            'tidak_bisa_diperbaiki' => 'Tidak Bisa Diperbaiki',
            default => $this->status,
        };
    }
}
