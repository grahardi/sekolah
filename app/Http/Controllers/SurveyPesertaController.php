<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Survey;
use App\Models\SurveyPeserta;
use Illuminate\Http\Request;

class SurveyPesertaController extends Controller
{
    public function index()
    {
        $pesertas = SurveyPeserta::with('survey')->latest()->paginate(15);
        return view('bk.peserta.index', ['pesertas' => $pesertas]);
    }

    public function create()
    {
        return view('bk.peserta.create', [
            'surveys' => Survey::orderByDesc('created_at')->get(['id', 'judul', 'status']),
            'kelasList' => $this->kelasRombelList(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'target_kelas' => 'required|array|min:1',
        ]);

        SurveyPeserta::create([
            'survey_id' => $validated['survey_id'],
            'target_kelas' => implode(',', $validated['target_kelas']),
        ]);

        return redirect()->route('bk.peserta.index')->with('success', 'Peserta survey berhasil ditambahkan.');
    }

    public function destroy(SurveyPeserta $peserta)
    {
        $peserta->delete();
        return back()->with('success', 'Data peserta dihapus.');
    }

    /** Daftar kombinasi kelas-rombel yang benar-benar ada di data siswa aktif */
    private function kelasRombelList()
    {
        return Siswa::where('status', 'aktif')
            ->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}-{$s->rombel}" : $s->kelas)
            ->unique()
            ->sort()
            ->values();
    }
}
