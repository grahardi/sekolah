<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SarprasAsset extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_assets';
    protected $fillable = [
        'sekolah_id', 'kode_barang', 'kode_umum', 'kode_aset', 'nama_barang',
        'category_id', 'location_id', 'tahun_pembelian',
        'funding_source_id', 'keterangan', 'status', 'foto',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }

    public function category()
    {
        return $this->belongsTo(SarprasCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(SarprasLocation::class, 'location_id');
    }

    public function fundingSource()
    {
        return $this->belongsTo(SarprasFundingSource::class, 'funding_source_id');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where(fn ($qq) => $qq
                ->where('nama_barang', 'ilike', "%{$v}%")
                ->orWhere('kode_barang', 'ilike', "%{$v}%")))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['location_id'] ?? null, fn ($q, $v) => $q->where('location_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v));
    }

    /** Generate nomor kode_barang berikutnya (per sekolah) - bisa diedit user setelahnya */
    public static function generateNextKodeBarang(int $sekolahId): string
    {
        // PostgreSQL: pakai CAST(... AS INTEGER), bukan UNSIGNED spt di MySQL asli
        $last = static::withoutGlobalScopes()
            ->where('sekolah_id', $sekolahId)
            ->whereRaw("kode_barang ~ '^[0-9]+$'") // cuma yg full angka, hindari error cast
            ->selectRaw('MAX(CAST(kode_barang AS INTEGER)) as max_kode')
            ->value('max_kode');

        $next = ($last ?? 0) + 1;

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
