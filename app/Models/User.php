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

#[Fillable(['name', 'email', 'password', 'password_plain', 'is_password_generated', 'sekolah_id', 'role', 'custom_role_id', 'is_super_admin'])]
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
            'is_password_generated' => 'boolean',
        ];
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    // ── Role helpers (dipakai modul Buku Induk) ─────────────────────────────
    public function customRole()
    {
        return $this->belongsTo(\App\Models\CustomRole::class);
    }

    /** Boleh akses modul ini? Admin selalu boleh. Kalau punya custom_role, cek permission-nya. Kalau enggak, fallback ke role lama (guru/induk gak boleh kecuali diatur eksplisit di modul masing2). */
    public function bolehAksesModul(string $modulKey): bool
    {
        if ($this->isAdmin()) return true;
        if (! $this->custom_role_id) return false;

        $perm = $this->customRole?->permissionMap()[$modulKey] ?? null;
        return $perm['boleh_akses'] ?? false;
    }

    /** Modul ini read-only buat user ini? (cuma relevan kalau bolehAksesModul() true) */
    public function modulReadOnly(string $modulKey): bool
    {
        if ($this->isAdmin()) return false;

        $perm = $this->customRole?->permissionMap()[$modulKey] ?? null;
        return $perm['read_only'] ?? true;
    }

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
