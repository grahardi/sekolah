<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExoRequest extends Model
{
    protected $table = 'exo_requests';
    protected $fillable = ['sekolah_id', 'diminta_oleh_user_id', 'catatan', 'status'];

    public function sekolah() { return $this->belongsTo(Sekolah::class); }
    public function diminta() { return $this->belongsTo(User::class, 'diminta_oleh_user_id'); }
}
