<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ModulAjar extends Model
{
    protected $table = 'modul_ajar';
    protected $fillable = [
        'mapel', 'kelas', 'fase', 'title', 'deskripsi', 'file_key', 'drive_id', 'urutan', 'aktif',
    ];
    protected $casts = ['aktif' => 'boolean'];

    public function getLocalPathAttribute(): string
    {
        return "modul-ajar/{$this->file_key}.docx";
    }

    public function getExistsLocallyAttribute(): bool
    {
        return Storage::disk('public')->exists($this->local_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return $this->exists_locally
            ? Storage::disk('public')->url($this->local_path)
            : "https://drive.google.com/uc?export=download&id={$this->drive_id}";
    }

    public function getSumberAttribute(): string
    {
        return $this->exists_locally ? 'Server sekolah.co.id' : 'Google Drive (Modul Guruku)';
    }
}
