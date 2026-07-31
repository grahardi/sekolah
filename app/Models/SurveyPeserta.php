<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyPeserta extends Model
{
    protected $table = 'survey_pesertas';
    protected $fillable = ['survey_id', 'target_kelas'];

    public function survey() { return $this->belongsTo(Survey::class); }

    public function getTargetKelasArrayAttribute(): array
    {
        return $this->target_kelas ? explode(',', $this->target_kelas) : [];
    }
}
