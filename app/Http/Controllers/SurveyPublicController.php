<?php

namespace App\Http\Controllers;

use App\Models\SurveyJawaban;
use App\Models\SurveyPeserta;
use Illuminate\Http\Request;

class SurveyPublicController extends Controller
{
    /**
     * Halaman awal: siswa masukkan NISN dulu utk verifikasi bahwa dia
     * termasuk peserta project ini (bukan pilih nama dari daftar - lebih
     * aman & lebih cepat untuk siswa banyak).
     */
    public function showForm(string $token)
    {
        $project = SurveyPeserta::withoutGlobalScopes()->where('token', $token)->firstOrFail();
        $survey = $project->survey()->withoutGlobalScopes()->firstOrFail();

        abort_if($survey->status !== 'aktif', 404, 'Survey ini belum aktif atau sudah ditutup.');

        return view('survey.nisn-check', ['project' => $project, 'survey' => $survey]);
    }

    /** Verifikasi NISN termasuk peserta project, lalu tampilkan form pertanyaan */
    public function verifikasiNisn(Request $request, string $token)
    {
        $project = SurveyPeserta::withoutGlobalScopes()->where('token', $token)->firstOrFail();
        $survey = $project->survey()->withoutGlobalScopes()->firstOrFail();
        abort_if($survey->status !== 'aktif', 404);

        $request->validate(['nisn' => 'required|string']);

        $siswa = $project->siswaTarget()->where('nisn', trim($request->nisn))->first();

        if (! $siswa) {
            return back()->withErrors(['nisn' => 'NISN tidak ditemukan di daftar peserta project ini. Pastikan kamu memang termasuk kelas yang dipilih guru.'])->withInput();
        }

        $sudahIsi = SurveyJawaban::where('peserta_id', $project->id)->where('siswa_id', $siswa->id)->exists();
        if ($sudahIsi) {
            return view('survey.sudah-selesai', ['survey' => $survey]);
        }

        $survey->load('pertanyaans');
        return view('survey.public-form', ['survey' => $survey, 'project' => $project, 'siswa' => $siswa]);
    }

    public function submit(Request $request, string $token)
    {
        $project = SurveyPeserta::withoutGlobalScopes()->where('token', $token)->firstOrFail();
        $survey = $project->survey()->withoutGlobalScopes()->firstOrFail();
        abort_if($survey->status !== 'aktif', 404);

        $request->validate(['siswa_id' => 'required|integer']);

        // Re-verifikasi siswa memang termasuk target project ini (jangan cuma
        // percaya siswa_id dari form - bisa dimanipulasi kalau tidak dicek ulang).
        $siswa = $project->siswaTarget()->where('id', $request->siswa_id)->first();
        abort_if(! $siswa, 403, 'Siswa tidak termasuk peserta project ini.');

        $existing = SurveyJawaban::where('peserta_id', $project->id)->where('siswa_id', $siswa->id)->first();
        if ($existing) {
            return view('survey.sudah-selesai', ['survey' => $survey]);
        }

        $survey->load('pertanyaans');
        $jawabanData = [];
        foreach ($survey->pertanyaans as $p) {
            $key = "jawaban_{$p->id}";
            $jawabanData[$p->id] = $p->tipe_jawaban === 'checklist'
                ? $request->input($key, [])
                : $request->input($key);
        }

        SurveyJawaban::create([
            'survey_id' => $survey->id,
            'peserta_id' => $project->id,
            'siswa_id' => $siswa->id,
            'data' => $jawabanData,
            'submitted_at' => now(),
        ]);

        return view('survey.terima-kasih', ['survey' => $survey]);
    }
}
