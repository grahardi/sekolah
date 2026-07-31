<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyJawaban;
use Illuminate\Http\Request;

class SurveyPublicController extends Controller
{
    public function showForm(string $token)
    {
        $survey = Survey::withoutGlobalScopes()->where('token', $token)->firstOrFail();

        abort_if($survey->status !== 'aktif', 404, 'Survey ini belum aktif atau sudah ditutup.');

        $survey->load('pertanyaans');
        $siswaTarget = $survey->siswaTarget()->orderBy('nama_lengkap')->get();

        return view('survey.public-form', ['survey' => $survey, 'siswaTarget' => $siswaTarget]);
    }

    public function submit(Request $request, string $token)
    {
        $survey = Survey::withoutGlobalScopes()->where('token', $token)->firstOrFail();
        abort_if($survey->status !== 'aktif', 404);

        $siswaIds = $survey->siswaTarget()->pluck('id')->toArray();

        $request->validate([
            'siswa_id' => 'required|in:' . implode(',', $siswaIds ?: [0]),
        ]);

        // Cegah isi dobel - kalau sudah pernah isi, tampilkan halaman "sudah selesai"
        $existing = SurveyJawaban::where('survey_id', $survey->id)
            ->where('siswa_id', $request->siswa_id)
            ->first();

        if ($existing) {
            return view('survey.sudah-selesai', ['survey' => $survey]);
        }

        $jawabanData = [];
        foreach ($survey->pertanyaans as $p) {
            $key = "jawaban_{$p->id}";
            if ($p->tipe_jawaban === 'checklist') {
                $jawabanData[$p->id] = $request->input($key, []);
            } else {
                $jawabanData[$p->id] = $request->input($key);
            }
        }

        SurveyJawaban::create([
            'survey_id' => $survey->id,
            'siswa_id' => $request->siswa_id,
            'data' => $jawabanData,
            'submitted_at' => now(),
        ]);

        return view('survey.terima-kasih', ['survey' => $survey]);
    }
}
