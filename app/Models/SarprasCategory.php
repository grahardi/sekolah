<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SarprasCategory extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_categories';
    protected $fillable = ['sekolah_id', 'name', 'slug', 'parent_id', 'order', 'icon'];

    // Daftar pilihan ikon (Tabler Icons - dipakai konsisten dgn seluruh sistem kita)
    public static function iconOptions(): array
    {
        return [
            'ti-device-laptop' => 'Komputer/Laptop',
            'ti-device-desktop' => 'PC/Desktop',
            'ti-printer' => 'Printer',
            'ti-projector' => 'Proyektor/LCD',
            'ti-table' => 'Meja',
            'ti-armchair' => 'Kursi',
            'ti-archive' => 'Lemari/Rak',
            'ti-book' => 'Buku/Perpustakaan',
            'ti-ball-basketball' => 'Alat Olahraga',
            'ti-music' => 'Alat Musik',
            'ti-flask' => 'Alat Lab/Sains',
            'ti-trash' => 'Alat Kebersihan',
            'ti-bolt' => 'Elektronik/Listrik',
            'ti-car' => 'Kendaraan',
            'ti-building' => 'Bangunan/Gedung',
            'ti-folder' => 'Umum/Lainnya',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name, $category->sekolah_id);
            }
        });
    }

    public static function generateUniqueSlug($name, $sekolahId, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->where('sekolah_id', $sekolahId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    public function parent()
    {
        return $this->belongsTo(SarprasCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SarprasCategory::class, 'parent_id')->orderBy('order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function assets()
    {
        return $this->hasMany(SarprasAsset::class, 'category_id');
    }

    /** Ambil semua kategori dlm bentuk tree (root only, children dimuat rekursif) */
    public static function tree()
    {
        return static::whereNull('parent_id')->orderBy('order')->with('childrenRecursive')->get();
    }
}
