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

    public function show(SurveyPeserta $peserta)
    {
        $peserta->load(['survey.pertanyaans']);
        $siswaTarget = $peserta->siswaTarget()->orderBy('nama_lengkap')->get();
        $jawabans = $peserta->jawabans()->get();
        $sudahIsi = $jawabans->pluck('siswa_id')->toArray();

        // Analisa otomatis: persentase "bermasalah" per kategori pertanyaan
        // (khusus pertanyaan tipe checklist 2-opsi ala DCM: "Ya, saya alami" vs "Tidak")
        $analisa = [];
        foreach ($peserta->survey->pertanyaans->groupBy('kategori') as $kategori => $pertanyaans) {
            $totalYa = 0;
            $totalJawaban = 0;
            foreach ($pertanyaans as $p) {
                foreach ($jawabans as $j) {
                    $val = $j->data[$p->id] ?? null;
                    if ($val === null) continue;
                    $totalJawaban++;
                    $isYa = is_array($val) ? in_array('Ya, saya alami', $val) : $val === 'Ya, saya alami';
                    if ($isYa) $totalYa++;
                }
            }
            $analisa[] = [
                'kategori' => $kategori ?: 'Umum',
                'persentase' => $totalJawaban > 0 ? round(($totalYa / $totalJawaban) * 100) : 0,
                'total_jawaban' => $totalJawaban,
            ];
        }
        usort($analisa, fn ($a, $b) => $b['persentase'] <=> $a['persentase']);

        return view('bk.peserta.show', [
            'project' => $peserta,
            'siswaTarget' => $siswaTarget,
            'sudahIsi' => $sudahIsi,
            'analisa' => $analisa,
        ]);
    }

    public function hasilSiswa(SurveyPeserta $peserta, Siswa $siswa)
    {
        $jawaban = $peserta->jawabans()->where('siswa_id', $siswa->id)->firstOrFail();
        return view('bk.peserta.hasil-siswa', [
            'project' => $peserta->load('survey.pertanyaans'),
            'siswa' => $siswa,
            'jawaban' => $jawaban,
        ]);
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
