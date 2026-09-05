<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowcaseItem extends Model
{
    protected $table = 'showcase_items';
    protected $fillable = ['judul', 'subjudul', 'deskripsi', 'gambar', 'link', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
}
