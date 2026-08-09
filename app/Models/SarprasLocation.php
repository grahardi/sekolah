<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;

class SarprasLocation extends Model
{
    use BelongsToSekolah;

    protected $table = 'sarpras_locations';
    protected $fillable = ['sekolah_id', 'name', 'keterangan'];

    public function assets()
    {
        return $this->hasMany(SarprasAsset::class, 'location_id');
    }
}
