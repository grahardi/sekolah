<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporDetailEkskul extends Model
{
    protected $table = 'rapor_detail_ekskuls';
    protected $fillable = ['rapor_id', 'nama_ekskul', 'keterangan'];

    public function rapor() { return $this->belongsTo(Rapor::class); }
}
