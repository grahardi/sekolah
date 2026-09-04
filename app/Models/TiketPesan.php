<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiketPesan extends Model
{
    protected $table = 'tiket_pesan';
    protected $fillable = ['tiket_id', 'user_id', 'dari_superadmin', 'pesan'];

    protected $casts = ['dari_superadmin' => 'boolean'];

    public function tiket()
    {
        return $this->belongsTo(TiketDukungan::class, 'tiket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
