<?php

namespace App\Http\Controllers;

use App\Models\{Siswa, NilaiRapor, NilaiP5, NilaiEkskul, Kehadiran, RiwayatKelas};
use App\Imports\NilaiRaporImport;
use App\Exports\NilaiRaporExport;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Siswa $siswa)
    {
        $siswa->load(['nilaiRapors']);
        return view('nilai.index', compact('siswa'));
    }

    // ── Rapor (input manual per siswa, disimpan untuk kelengkapan API) ─────────
    public function storeRapor(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tahun_ajaran'           => 'required|string|max:10',
            'semester'               => 'required|in:1,2',
            'kelas'                  => 'required|string|max:10',
            'nilai'                  => 'required|array',
            'nilai.*.mata_pelajaran' => 'required|string|max:80',
            'nilai.*.kelompok'       => 'nullable|string|max:30',
            'nilai.*.nilai'          => 'nullable|numeric|min:0|max:100',
            'nilai.*.deskripsi'      => 'nullable|string|max:500',
        ]);

        foreach ($data['nilai'] as $item) {
            if (empty($item['mata_pelajaran'])) continue;
            NilaiRapor::updateOrCreate(
                [
                    'siswa_id'       => $siswa->id,
                    'tahun_ajaran'   => $data['tahun_ajaran'],
                    'semester'       => $data['semester'],
                    'mata_pelajaran' => $item['mata_pelajaran'],
                ],
                [
                    'kelas'     => $data['kelas'],
                    'kelompok'  => $item['kelompok'] ?? 'Umum',
                    'nilai'     => $item['nilai'] ?? null,
                    'deskripsi' => $item['deskripsi'] ?? null,
                ]
            );
        }

        return back()->with('success','Nilai rapor berhasil disimpan.');
    }

    public function destroyRapor(Siswa $siswa, NilaiRapor $nilaiRapor)
    {
        $nilaiRapor->delete();
        return back()->with('success','Nilai dihapus.');
    }

    // ── P5 ───────────────────────────────────────────────────────────────────
    public function storeP5(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tahun_ajaran' => 'required|string|max:10',
            'semester'     => 'required|in:1,2',
            'kelas'        => 'required|string|max:10',
            'tema_projek'  => 'required|string|max:100',
            'topik'        => 'nullable|string|max:100',
            'nilai'        => 'required|in:BB,MB,BSH,BSB',
            'deskripsi'    => 'nullable|string|max:500',
        ]);
        NilaiP5::create(array_merge($data, ['siswa_id' => $siswa->id]));
        return back()->with('success','Nilai P5 berhasil disimpan.');
    }

    public function destroyP5(Siswa $siswa, NilaiP5 $nilaiP5)
    {
        $nilaiP5->delete();
        return back()->with('success','Nilai P5 dihapus.');
    }

    // ── Ekskul ───────────────────────────────────────────────────────────────
    public function storeEkskul(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tahun_ajaran'     => 'required|string|max:10',
            'semester'         => 'required|in:1,2',
            'nama_ekskul'      => 'required|string|max:80',
            'nilai_kualitatif' => 'nullable|string|max:30',
            'keterangan'       => 'nullable|string|max:500',
        ]);
        NilaiEkskul::create(array_merge($data, ['siswa_id' => $siswa->id]));
        return back()->with('success','Nilai ekstrakurikuler disimpan.');
    }

    public function destroyEkskul(Siswa $siswa, NilaiEkskul $nilaiEkskul)
    {
        $nilaiEkskul->delete();
        return back()->with('success','Data ekskul dihapus.');
    }

    // ── Kehadiran ────────────────────────────────────────────────────────────
    public function storeKehadiran(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tahun_ajaran' => 'required|string|max:10',
            'semester'     => 'required|in:1,2',
            'kelas'        => 'required|string|max:10',
            'sakit'        => 'required|integer|min:0',
            'izin'         => 'required|integer|min:0',
            'alpa'         => 'required|integer|min:0',
        ]);
        Kehadiran::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran' => $data['tahun_ajaran'], 'semester' => $data['semester']],
            array_merge($data, ['siswa_id' => $siswa->id])
        );
        return back()->with('success','Data kehadiran disimpan.');
    }

    // ── Riwayat Kelas ────────────────────────────────────────────────────────
    public function storeRiwayat(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tahun_ajaran' => 'required|string|max:10',
            'kelas'        => 'required|string|max:10',
            'rombel'       => 'nullable|string|max:20',
            'wali_kelas'   => 'nullable|string|max:100',
            'hasil'        => 'nullable|in:Naik Kelas,Tinggal Kelas,Lulus,Pindah,Keluar',
            'catatan'      => 'nullable|string|max:500',
        ]);
        RiwayatKelas::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran' => $data['tahun_ajaran']],
            array_merge($data, ['siswa_id' => $siswa->id])
        );
        return back()->with('success','Riwayat kelas disimpan.');
    }

    // ── Import & Export Nilai Massal ────────────────────────────────────────
    public function showImportMassal()
    {
        $kelasList   = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
        return view('nilai.import-massal', compact('kelasList', 'tahunAjaran'));
    }

    public function importMassal(Request $request)
    {
        $request->validate([
            'file'         => 'required|mimes:xlsx,xls,csv|max:10240',
            'tahun_ajaran' => 'required|string|max:10',
            'semester'     => 'required|in:1,2',
        ]);

        $filePath = $request->file('file')->getRealPath();
        $import   = new NilaiRaporImport();
        $import->import($filePath, $request->tahun_ajaran, (int) $request->semester);

        $errors   = $import->getErrors();
        $warnings = $import->getWarnings();
        $imported = $import->getImportedCount();
        $skipped  = $import->getSkippedCount();

        $msg  = "Berhasil: {$imported} siswa nilai tersimpan.";
        if ($skipped > 0)       $msg .= " {$skipped} baris dilewati.";
        if (count($errors) > 0) $msg .= " " . count($errors) . " error.";

        $type = count($errors) > 0 ? 'warning' : ($imported > 0 ? 'success' : 'warning');

        return redirect()->route('nilai.import-massal')
            ->with($type, $msg)
            ->with('import_errors',   $errors)
            ->with('import_warnings', $warnings)
            ->with('import_imported', $imported);
    }

    public function templateMassal(Request $request)
    {
        $request->validate([
            'kelas'        => 'required|string|max:10',
            'tahun_ajaran' => 'required|string|max:10',
            'semester'     => 'required|in:1,2',
        ]);

        return (new NilaiRaporExport())->downloadTemplate(
            $request->kelas,
            $request->tahun_ajaran,
            (int) $request->semester
        );
    }
}
