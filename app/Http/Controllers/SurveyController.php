<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Survey;
use App\Models\SurveyPertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount(['jawabans'])->latest()->paginate(15);
        return view('bk.survey.index', ['surveys' => $surveys]);
    }

    public function create()
    {
        return view('bk.survey.create', ['kelasList' => $this->kelasRombelList()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'jenis' => 'required|in:DCM,AUM,Custom',
            'status' => 'required|in:draft,aktif,ditutup',
            'target_kelas' => 'nullable|array',
            'pertanyaan' => 'required|array|min:1',
            'pertanyaan.*.teks' => 'required|string',
            'pertanyaan.*.tipe' => 'required|in:pilihan_ganda,checklist,skala,esai',
            'pertanyaan.*.opsi' => 'nullable|string',
            'pertanyaan.*.kategori' => 'nullable|string|max:50',
        ]);

        $survey = Survey::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jenis' => $validated['jenis'],
            'status' => $validated['status'],
            'target_kelas' => isset($validated['target_kelas']) ? implode(',', $validated['target_kelas']) : null,
        ]);

        $this->savePertanyaans($survey, $validated['pertanyaan']);

        return redirect()->route('bk.survey.show', $survey)->with('success', 'Survey berhasil dibuat.');
    }

    public function edit(Survey $survey)
    {
        $survey->load('pertanyaans');
        return view('bk.survey.edit', ['survey' => $survey, 'kelasList' => $this->kelasRombelList()]);
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'jenis' => 'required|in:DCM,AUM,Custom',
            'status' => 'required|in:draft,aktif,ditutup',
            'target_kelas' => 'nullable|array',
            'pertanyaan' => 'required|array|min:1',
            'pertanyaan.*.teks' => 'required|string',
            'pertanyaan.*.tipe' => 'required|in:pilihan_ganda,checklist,skala,esai',
            'pertanyaan.*.opsi' => 'nullable|string',
            'pertanyaan.*.kategori' => 'nullable|string|max:50',
        ]);

        $survey->update([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jenis' => $validated['jenis'],
            'status' => $validated['status'],
            'target_kelas' => isset($validated['target_kelas']) ? implode(',', $validated['target_kelas']) : null,
        ]);

        // Ganti seluruh pertanyaan (lebih simpel drpd diff - aman krn jawaban
        // disimpan sbg snapshot JSON per siswa, bukan relasi ketat ke pertanyaan_id)
        $survey->pertanyaans()->delete();
        $this->savePertanyaans($survey, $validated['pertanyaan']);

        return redirect()->route('bk.survey.show', $survey)->with('success', 'Survey berhasil diperbarui.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route('bk.survey.index')->with('success', 'Survey berhasil dihapus.');
    }

    public function show(Survey $survey)
    {
        $survey->load('pertanyaans');

        $siswaTarget = $survey->siswaTarget()->orderBy('nama_lengkap')->get();
        $sudahIsi = $survey->jawabans()->pluck('siswa_id')->toArray();

        return view('bk.survey.show', [
            'survey' => $survey,
            'siswaTarget' => $siswaTarget,
            'sudahIsi' => $sudahIsi,
        ]);
    }

    public function hasilSiswa(Survey $survey, Siswa $siswa)
    {
        $jawaban = $survey->jawabans()->where('siswa_id', $siswa->id)->firstOrFail();
        return view('bk.survey.hasil-siswa', [
            'survey' => $survey->load('pertanyaans'),
            'siswa' => $siswa,
            'jawaban' => $jawaban,
        ]);
    }

    private function savePertanyaans(Survey $survey, array $pertanyaans): void
    {
        foreach ($pertanyaans as $i => $p) {
            $opsi = null;
            if (in_array($p['tipe'], ['pilihan_ganda', 'checklist']) && !empty($p['opsi'])) {
                $opsi = array_values(array_filter(array_map('trim', explode("\n", $p['opsi']))));
            }

            SurveyPertanyaan::create([
                'survey_id' => $survey->id,
                'urutan' => $i,
                'teks_pertanyaan' => $p['teks'],
                'tipe_jawaban' => $p['tipe'],
                'opsi' => $opsi,
                'kategori' => $p['kategori'] ?? null,
            ]);
        }
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
