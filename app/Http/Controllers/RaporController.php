<?php

namespace App\Http\Controllers;

use App\Models\GuruPengajar;
use App\Models\MataPelajaran;
use App\Models\Rapor;
use App\Models\RaporDetailAkademik;
use App\Models\RaporDetailEkskul;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\RaporCalculator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RaporController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $semester = (int) $request->input('semester', $tahunAjaran?->semester === 'Genap' ? 2 : 1);
        $kelasRombel = $request->input('kelas_rombel');

        $kelasList = $this->kelasRombelList();
        $siswaList = collect();

        if ($tahunAjaran && $kelasRombel) {
            [$kelas, $rombel] = array_pad(explode('|', $kelasRombel), 2, null);
            $siswaList = Siswa::where('status', 'aktif')
                ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->orderBy('nama_lengkap')->get();

            $raporMap = Rapor::where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('semester', $semester)
                ->whereIn('siswa_id', $siswaList->pluck('id'))
                ->get()->keyBy('siswa_id');

            $siswaList->each(function ($s) use ($raporMap) {
                $s->rapor = $raporMap->get($s->id);
            });
        }

        return view('erapor.rapor.index', [
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester,
            'kelasList' => $kelasList,
            'kelasRombel' => $kelasRombel,
            'siswaList' => $siswaList,
        ]);
    }

    public function generateKelas(Request $request)
    {
        $request->validate(['kelas_rombel' => 'required|string', 'semester' => 'required|integer|in:1,2']);

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422, 'Belum ada tahun ajaran aktif.');

        [$kelas, $rombel] = array_pad(explode('|', $request->kelas_rombel), 2, null);
        $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)->get();

        foreach ($siswaList as $siswa) {
            $this->generateSatuSiswa($siswa, $tahunAjaran, (int) $request->semester, $kelas, $rombel);
        }

        return back()->with('success', "Rapor {$siswaList->count()} siswa berhasil dihitung/diperbarui.");
    }

    private function generateSatuSiswa(Siswa $siswa, TahunAjaran $tahunAjaran, int $semester, string $kelas, ?string $rombel): Rapor
    {
        $rapor = Rapor::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'semester' => $semester],
            ['kelas' => $kelas, 'rombel' => $rombel]
        );

        // Mapel yang diajarkan di kelas ini pada tahun ajaran ini (dari Guru Pengajar)
        $mapelIds = GuruPengajar::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kelas', $kelas)->where('rombel', $rombel)
            ->pluck('mata_pelajaran_id')->unique();

        foreach ($mapelIds as $mapelId) {
            $hasil = RaporCalculator::hitung($siswa->id, $kelas, $rombel, $mapelId, $tahunAjaran->id, $semester);

            RaporDetailAkademik::updateOrCreate(
                ['rapor_id' => $rapor->id, 'mata_pelajaran_id' => $mapelId],
                ['nilai_akhir' => $hasil['nilai_akhir'], 'capaian_kompetensi' => $hasil['deskripsi']]
            );
        }

        return $rapor;
    }

    public function edit(Rapor $rapor)
    {
        $rapor->load(['siswa', 'detailAkademik.mataPelajaran', 'detailEkskul']);
        return view('erapor.rapor.edit', ['rapor' => $rapor]);
    }

    public function update(Request $request, Rapor $rapor)
    {
        $data = $request->validate([
            'sakit' => 'nullable|integer|min:0',
            'izin' => 'nullable|integer|min:0',
            'tanpa_keterangan' => 'nullable|integer|min:0',
            'catatan_wali_kelas' => 'nullable|string',
            'deskripsi_kokurikuler' => 'nullable|string',
            'status' => 'required|in:Draft,Final',
            'keterangan_kelulusan' => 'nullable|string|max:100',
            'tanggal_rapor' => 'nullable|date',
            'nilai_katrol' => 'nullable|array',
            'ekskul_nama' => 'nullable|array',
            'ekskul_keterangan' => 'nullable|array',
        ]);

        $rapor->update([
            'sakit' => $data['sakit'] ?? 0,
            'izin' => $data['izin'] ?? 0,
            'tanpa_keterangan' => $data['tanpa_keterangan'] ?? 0,
            'catatan_wali_kelas' => $data['catatan_wali_kelas'] ?? null,
            'deskripsi_kokurikuler' => $data['deskripsi_kokurikuler'] ?? null,
            'status' => $data['status'],
            'keterangan_kelulusan' => $data['keterangan_kelulusan'] ?? null,
            'tanggal_rapor' => $data['tanggal_rapor'] ?? null,
        ]);

        // Nilai katrol (koreksi manual, opsional) per mapel
        if (! empty($data['nilai_katrol'])) {
            foreach ($data['nilai_katrol'] as $detailId => $nilai) {
                if ($nilai === null || $nilai === '') continue;
                RaporDetailAkademik::where('id', $detailId)->where('rapor_id', $rapor->id)->update(['nilai_katrol' => $nilai]);
            }
        }

        // Ekstrakurikuler manual (tambah/kelola baris)
        $rapor->detailEkskul()->delete();
        if (! empty($data['ekskul_nama'])) {
            foreach ($data['ekskul_nama'] as $i => $nama) {
                if (empty($nama)) continue;
                RaporDetailEkskul::create([
                    'rapor_id' => $rapor->id,
                    'nama_ekskul' => $nama,
                    'keterangan' => $data['ekskul_keterangan'][$i] ?? null,
                ]);
            }
        }

        return redirect()->route('erapor.rapor.edit', $rapor)->with('success', 'Rapor berhasil disimpan.');
    }

    public function cetak(Rapor $rapor)
    {
        ini_set('memory_limit', '512M');
        $rapor->load(['siswa', 'detailAkademik.mataPelajaran', 'detailEkskul']);
        $sekolah = auth()->user()->sekolah;

        $waliKelas = \App\Models\WaliKelas::with('guru')
            ->where('tahun_ajaran_id', $rapor->tahun_ajaran_id)
            ->where('kelas', $rapor->kelas)->where('rombel', $rapor->rombel)
            ->first();

        $pdf = Pdf::loadView('erapor.rapor.pdf', [
            'rapor' => $rapor,
            'sekolah' => $sekolah,
            'waliKelas' => $waliKelas?->guru,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('rapor-' . str_replace(' ', '-', $rapor->siswa->nama_lengkap) . '.pdf');
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
