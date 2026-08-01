<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'password_plain', 'sekolah_id', 'role', 'is_super_admin'])]
#[Hidden(['password', 'password_plain', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    // ── Role helpers (dipakai modul Buku Induk) ─────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInduk(): bool
    {
        return $this->role === 'induk';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin Sekolah',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
            'induk' => 'Petugas Induk (Read Only)',
            default => 'Pengguna',
        };
    }
}
