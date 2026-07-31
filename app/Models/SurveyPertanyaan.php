<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyPertanyaan extends Model
{
    protected $table = 'survey_pertanyaans';
    protected $fillable = ['survey_id', 'urutan', 'teks_pertanyaan', 'tipe_jawaban', 'opsi', 'kategori'];
    protected $casts = ['opsi' => 'array'];

    public function survey() { return $this->belongsTo(Survey::class); }
}
