<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyJawaban extends Model
{
    protected $table = 'survey_jawabans';
    protected $fillable = ['survey_id', 'siswa_id', 'data', 'submitted_at'];
    protected $casts = ['data' => 'array', 'submitted_at' => 'datetime'];

    public function survey() { return $this->belongsTo(Survey::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
}
