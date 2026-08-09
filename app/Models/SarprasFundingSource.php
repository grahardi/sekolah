<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class SarprasFundingSource extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_funding_sources';
    protected $fillable = ['sekolah_id', 'name', 'keterangan'];

    public function assets()
    {
        return $this->hasMany(SarprasAsset::class, 'funding_source_id');
    }
}
