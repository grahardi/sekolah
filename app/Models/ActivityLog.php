<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'sekolah_id', 'nama_snapshot', 'email_snapshot',
        'event', 'ip_address', 'user_agent',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function sekolah() { return $this->belongsTo(Sekolah::class); }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'login' => 'Login',
            'logout' => 'Logout',
            'registrasi' => 'Registrasi',
            default => $this->event,
        };
    }
}
