<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruPengajar;
use App\Models\MataPelajaran;
use App\Models\Penilaian;
use App\Models\PenilaianDetailNilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TpKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['kelas_rombel', 'mata_pelajaran_id']);
        $user = auth()->user();

        // ── Kalau mapel+kelas sudah dipilih -> tampilkan "Bank Nilai" lengkap ──
        if (($filters['mata_pelajaran_id'] ?? null) && ($filters['kelas_rombel'] ?? null)) {
            return $this->bankNilai($filters['mata_pelajaran_id'], $filters['kelas_rombel']);
        }

        // ── Kalau belum -> tampilkan kartu kelas (landing page) ──
        $kombinasiQuery = GuruPengajar::query();
        if ($user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $kombinasiQuery->where('guru_id', $guru?->id ?? 0);
        }

        $kombinasi = $kombinasiQuery->with('mataPelajaran')->get()
            ->unique(fn ($p) => $p->mata_pelajaran_id . '|' . $p->kelas . '|' . $p->rombel)
            ->map(function ($p) {
                $kelasRombel = $p->rombel ? "{$p->kelas}|{$p->rombel}" : "{$p->kelas}|";
                return [
                    'mapel_id' => $p->mata_pelajaran_id,
                    'mapel_nama' => $p->mataPelajaran->nama,
                    'kelas' => $p->kelas,
                    'rombel' => $p->rombel,
                    'kelas_rombel' => $kelasRombel,
                    'jumlah_penilaian' => Penilaian::where('mata_pelajaran_id', $p->mata_pelajaran_id)->where('kelas', $p->kelas)->where('rombel', $p->rombel)->count(),
                    'jumlah_tp' => TpKelas::where('kelas', $p->kelas)->where('rombel', $p->rombel)
                        ->whereHas('tujuanPembelajaran', fn ($q) => $q->where('mata_pelajaran_id', $p->mata_pelajaran_id))->count(),
                ];
            })->values();

        return view('erapor.penilaian.kartu-kelas', ['kombinasi' => $kombinasi]);
    }

    /** "Bank Nilai" - rekap penilaian + ringkasan nilai akhir & deskripsi 1 kelas+mapel */
    private function bankNilai($mapelId, $kelasRombel)
    {
        [$kelas, $rombel] = array_pad(explode('|', $kelasRombel), 2, null);
        $mapel = MataPelajaran::findOrFail($mapelId);
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');
        $semester = $tahunAktif->semester === 'Genap' ? 2 : 1;

        $penilaianList = Penilaian::where('mata_pelajaran_id', $mapelId)
            ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
            ->with('tujuanPembelajarans')
            ->withCount('nilais')
            ->orderByRaw("CASE subjenis_penilaian
                WHEN 'Sumatif TP' THEN 1
                WHEN 'Sumatif Tengah Semester' THEN 2
                WHEN 'Sumatif Akhir Semester' THEN 3
                ELSE 4 END")
            ->get();

        $sumatif = $penilaianList->where('jenis_penilaian', 'Sumatif');
        $formatif = $penilaianList->where('jenis_penilaian', 'Formatif');

        $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)
            ->orderBy('nis')->orderBy('nama_lengkap')->get();

        $nilaiMap = PenilaianDetailNilai::whereIn('penilaian_id', $sumatif->pluck('id'))
            ->get()->groupBy('penilaian_id')->map(fn ($g) => $g->pluck('nilai', 'siswa_id'));

        $rekap = $siswaList->map(function ($s) use ($sumatif, $nilaiMap, $kelas, $rombel, $mapelId, $tahunAktif, $semester) {
            $hasil = \App\Services\RaporCalculator::hitung($s->id, $kelas, $rombel, $mapelId, $tahunAktif->id, $semester);
            return [
                'siswa' => $s,
                'nilai_per_penilaian' => $sumatif->mapWithKeys(fn ($p) => [$p->id => $nilaiMap->get($p->id)?->get($s->id)]),
                'nilai_rapor' => $hasil['nilai_akhir'],
                'deskripsi' => $hasil['deskripsi'],
            ];
        });

        return view('erapor.penilaian.bank-nilai', [
            'mapel' => $mapel,
            'kelas' => $kelas,
            'rombel' => $rombel,
            'kelasRombel' => $kelasRombel,
            'sumatif' => $sumatif,
            'formatif' => $formatif,
            'rekap' => $rekap,
            'kkm' => auth()->user()->sekolah->kkm ?? 75,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $prefillMapel = $request->input('mata_pelajaran_id');
        $prefillKelas = $request->input('kelas_rombel');

        if ($user->role === 'guru') {
            $guruSaya = Guru::where('user_id', $user->id)->first();
            $penugasan = $guruSaya
                ? GuruPengajar::with('mataPelajaran')->where('guru_id', $guruSaya->id)->get()
                : collect();

            return view('erapor.penilaian.create', [
                'guruSaya' => $guruSaya,
                'guruList' => null,
                'mapelList' => $penugasan->pluck('mataPelajaran')->unique('id')->values(),
                'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
                'kelasList' => $penugasan->map(fn ($p) => $p->rombel ? "{$p->kelas}|{$p->rombel}" : "{$p->kelas}|")->unique()->values(),
                'penugasanSaya' => $penugasan,
                'prefillMapel' => $prefillMapel,
                'prefillKelas' => $prefillKelas,
            ]);
        }

        return view('erapor.penilaian.create', [
            'guruSaya' => null,
            'guruList' => Guru::orderBy('nama')->get(),
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'prefillMapel' => $prefillMapel,
            'prefillKelas' => $prefillKelas,
            'penugasanSaya' => null,
        ]);
    }

    /** Ambil daftar TP yg cocok utk mapel+kelas+semester tertentu (dipanggil via AJAX dari form create) */
    public function tpUntukKonteks(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
            'rombel' => 'nullable|string',
            'semester' => 'required|integer',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $tps = TujuanPembelajaran::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->whereHas('kelasList', function ($q) use ($request) {
                $q->where('kelas', $request->kelas)->where('rombel', $request->rombel ?: null);
            })
            ->get(['id', 'kode_tp', 'deskripsi_tp']);

        return response()->json($tps);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas_rombel' => 'required|string',
            'nama_penilaian' => 'required|string|max:150',
            'subjenis_penilaian' => 'required|in:Sumatif TP,Sumatif Tengah Semester,Sumatif Akhir Semester',
            'bobot_penilaian' => 'required|integer|min:1|max:100',
            'semester' => 'required|integer|in:1,2',
            'tanggal_penilaian' => 'nullable|date',
            'tp_ids' => 'nullable|array',
        ]);

        [$kelas, $rombel] = array_pad(explode('|', $data['kelas_rombel']), 2, null);

        // PTS (Sumatif Tengah Semester) & UAS (Sumatif Akhir Semester) maks 1
        // per guru+mapel+kelas+semester - gak boleh dobel.
        if (in_array($data['subjenis_penilaian'], ['Sumatif Tengah Semester', 'Sumatif Akhir Semester'])) {
            $sudahAda = Penilaian::where('guru_id', $data['guru_id'])
                ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
                ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                ->where('semester', $data['semester'])
                ->where('subjenis_penilaian', $data['subjenis_penilaian'])
                ->exists();

            if ($sudahAda) {
                $label = $data['subjenis_penilaian'] === 'Sumatif Tengah Semester' ? 'Penilaian Tengah Semester (PTS)' : 'Penilaian Semester Akhir (UAS)';
                return back()->withInput()->withErrors(['subjenis_penilaian' => "{$label} untuk mapel & kelas ini di semester tsb sudah ada. Maksimal 1, edit yang sudah ada saja."]);
            }
        }

        // Guru cuma boleh buat penilaian utk dirinya sendiri, di mapel+kelas
        // yg memang ditugaskan admin - jangan cuma percaya UI.
        $user = auth()->user();
        if ($user->role === 'guru') {
            $guruSaya = Guru::where('user_id', $user->id)->first();
            abort_unless($guruSaya && $guruSaya->id == $data['guru_id'], 403, 'Kamu cuma bisa membuat penilaian atas namamu sendiri.');

            $adaPenugasan = GuruPengajar::where('guru_id', $guruSaya->id)
                ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
                ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->exists();
            abort_unless($adaPenugasan, 403, 'Kamu tidak ditugaskan mengajar mapel/kelas ini.');
        }

        $penilaian = Penilaian::create([
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
            'guru_id' => $data['guru_id'],
            'mata_pelajaran_id' => $data['mata_pelajaran_id'],
            'kelas' => $kelas,
            'rombel' => $rombel ?: null,
            'nama_penilaian' => $data['nama_penilaian'],
            'jenis_penilaian' => 'Sumatif', // Formatif dinonaktifkan sementara
            'subjenis_penilaian' => $data['subjenis_penilaian'],
            'bobot_penilaian' => $data['bobot_penilaian'],
            'semester' => $data['semester'],
            'tanggal_penilaian' => $data['tanggal_penilaian'] ?? null,
        ]);

        if ($data['subjenis_penilaian'] === 'Sumatif TP' && ! empty($data['tp_ids'])) {
            $penilaian->tujuanPembelajarans()->sync($data['tp_ids']);
        }

        return redirect()->route('erapor.penilaian.show', $penilaian)->with('success', 'Penilaian dibuat. Sekarang input nilai siswa.');
    }

    public function show(Penilaian $penilaian)
    {
        $penilaian->load(['mataPelajaran', 'guru', 'tujuanPembelajarans']);

        $siswaList = Siswa::where('status', 'aktif')
            ->where('kelas', $penilaian->kelas)
            ->where('rombel', $penilaian->rombel)
            ->orderBy('nis')->orderBy('nama_lengkap')
            ->get();

        $nilaiExisting = PenilaianDetailNilai::where('penilaian_id', $penilaian->id)
            ->pluck('nilai', 'siswa_id');

        return view('erapor.penilaian.show', [
            'penilaian' => $penilaian,
            'siswaList' => $siswaList,
            'nilaiExisting' => $nilaiExisting,
            'kkm' => auth()->user()->sekolah->kkm ?? 75,
        ]);
    }

    public function saveNilai(Request $request, Penilaian $penilaian)
    {
        $data = $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'nullable|integer|min:0|max:100',
        ]);

        foreach ($data['nilai'] as $siswaId => $nilai) {
            if ($nilai === null || $nilai === '') continue;
            PenilaianDetailNilai::updateOrCreate(
                ['penilaian_id' => $penilaian->id, 'siswa_id' => $siswaId],
                ['nilai' => $nilai]
            );
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    // ── Import/Export Nilai (per-penilaian & multi-kolom per-kelas) ─────

    /** Download template nilai utk 1 penilaian - sudah ada nama, tinggal isi nilai */
    public function downloadTemplateNilai(Penilaian $penilaian)
    {
        $siswaList = Siswa::where('status', 'aktif')
            ->where('kelas', $penilaian->kelas)->where('rombel', $penilaian->rombel)
            ->orderBy('nis')->orderBy('nama_lengkap')->get();

        $nilaiExisting = PenilaianDetailNilai::where('penilaian_id', $penilaian->id)->pluck('nilai', 'siswa_id');

        $rows = $siswaList->map(fn ($s) => [
            'no_induk' => $s->nis,
            'nama' => $s->nama_lengkap,
            'nilai' => $nilaiExisting[$s->id] ?? '',
        ]);

        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download('template-nilai-' . str_replace(' ', '-', $penilaian->nama_penilaian) . '.xlsx');
    }

    public function importNilai(Request $request, Penilaian $penilaian)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $diperbarui = 0;
        (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file')->getRealPath(), function (array $row) use ($penilaian, &$diperbarui) {
            $noInduk = trim($row['no_induk'] ?? '');
            $nilai = $row['nilai'] ?? null;
            if ($noInduk === '' || $nilai === null || $nilai === '') return;

            $siswa = Siswa::where('nis', $noInduk)->where('kelas', $penilaian->kelas)->where('rombel', $penilaian->rombel)->first();
            if (! $siswa) return;

            PenilaianDetailNilai::updateOrCreate(
                ['penilaian_id' => $penilaian->id, 'siswa_id' => $siswa->id],
                ['nilai' => (int) $nilai]
            );
            $diperbarui++;
        });

        return redirect()->route('erapor.penilaian.show', $penilaian)->with('success', "{$diperbarui} nilai berhasil diimport.");
    }

    /** Download template SEMUA penilaian 1 kelas+mapel sekaligus (multi-kolom, termasuk PTS/UAS) */
    public function downloadTemplateKelas(Request $request)
    {
        $request->validate(['mata_pelajaran_id' => 'required|exists:mata_pelajarans,id', 'kelas_rombel' => 'required|string']);
        [$kelas, $rombel] = array_pad(explode('|', $request->kelas_rombel), 2, null);

        $penilaianList = Penilaian::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
            ->orderBy('jenis_penilaian')->orderBy('id')->get();

        $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)
            ->orderBy('nis')->orderBy('nama_lengkap')->get();

        $nilaiMap = PenilaianDetailNilai::whereIn('penilaian_id', $penilaianList->pluck('id'))
            ->get()->groupBy('penilaian_id')->map(fn ($g) => $g->pluck('nilai', 'siswa_id'));

        $rows = $siswaList->map(function ($s) use ($penilaianList, $nilaiMap) {
            $row = ['no_induk' => $s->nis, 'nama' => $s->nama_lengkap];
            foreach ($penilaianList as $p) {
                $row[$p->nama_penilaian] = $nilaiMap->get($p->id)?->get($s->id) ?? '';
            }
            return $row;
        });

        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download('template-nilai-kelas-' . str_replace('|', '-', $request->kelas_rombel) . '.xlsx');
    }

    public function importTemplateKelas(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas_rombel' => 'required|string',
        ]);
        [$kelas, $rombel] = array_pad(explode('|', $request->kelas_rombel), 2, null);

        // FastExcel otomatis snake_case-kan header kolom pas import (mis. "SAS
        // Genap" jadi "sas_genap") - siapkan 2 peta lookup (asli & slug) biar
        // tetap ketemu apapun format yang dihasilkan library saat baca file.
        $penilaianAsli = Penilaian::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas', $kelas)->where('rombel', $rombel ?: null)->get();
        $penilaianSlug = $penilaianAsli->keyBy(fn ($p) => \Illuminate\Support\Str::slug($p->nama_penilaian, '_'));
        $penilaianRaw = $penilaianAsli->keyBy('nama_penilaian');

        $diperbarui = 0;
        $kolomTakDikenali = collect();
        (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file')->getRealPath(), function (array $row) use ($kelas, $rombel, $penilaianSlug, $penilaianRaw, &$diperbarui, &$kolomTakDikenali) {
            $noInduk = trim($row['no_induk'] ?? '');
            if ($noInduk === '') return;

            $siswa = Siswa::where('nis', $noInduk)->where('kelas', $kelas)->where('rombel', $rombel ?: null)->first();
            if (! $siswa) return;

            foreach ($row as $kolom => $nilai) {
                if (in_array($kolom, ['no_induk', 'nama']) || $nilai === null || $nilai === '') continue;
                $penilaian = $penilaianSlug->get($kolom) ?? $penilaianRaw->get($kolom);
                if (! $penilaian) { $kolomTakDikenali->push($kolom); continue; }

                PenilaianDetailNilai::updateOrCreate(
                    ['penilaian_id' => $penilaian->id, 'siswa_id' => $siswa->id],
                    ['nilai' => (int) $nilai]
                );
                $diperbarui++;
            }
        });

        $pesan = "{$diperbarui} nilai berhasil diimport dari template kelas.";
        if ($diperbarui === 0 && $kolomTakDikenali->isNotEmpty()) {
            $pesan .= ' Kolom yang tidak dikenali: ' . $kolomTakDikenali->unique()->implode(', ') . '. Pastikan tidak mengubah nama header kolom di template.';
        }

        return redirect()->route('erapor.penilaian.index', ['mata_pelajaran_id' => $request->mata_pelajaran_id, 'kelas_rombel' => $request->kelas_rombel])
            ->with($diperbarui > 0 ? 'success' : 'warning', $pesan);
    }

    public function destroy(Penilaian $penilaian)
    {
        $penilaian->delete();
        return redirect()->route('erapor.penilaian.index')->with('success', 'Penilaian dihapus.');
    }

    private function kelasRombelList()
    {
        return Siswa::where('status', 'aktif')
            ->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();
    }
}
