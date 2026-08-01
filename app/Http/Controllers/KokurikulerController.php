<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\KokurikulerAsesmen;
use App\Models\KokurikulerKegiatan;
use App\Models\KokurikulerKelasTerlibat;
use App\Models\KokurikulerMapelTerlibat;
use App\Models\KokurikulerTargetDimensi;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KokurikulerController extends Controller
{
    // ── Kegiatan (CRUD) ──────────────────────────────────────────────
    public function index()
    {
        return view('erapor.kokurikuler.index', [
            'kegiatans' => KokurikulerKegiatan::with(['koordinator', 'kelasTerlibats', 'targetDimensis'])->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return view('erapor.kokurikuler.create', [
            'guruList' => Guru::orderBy('nama')->get(),
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'dimensiList' => KokurikulerKegiatan::daftarDimensi(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tema' => 'nullable|string|max:150',
            'bentuk_kegiatan' => 'required|in:Lintas Disiplin,G7KAIH,Khas Sekolah',
            'koordinator_guru_id' => 'required|exists:gurus,id',
            'semester' => 'required|integer|in:1,2',
            'dimensi' => 'required|array|min:1',
            'mapel_terlibat' => 'required|array|min:1',
            'kelas_sasaran' => 'required|array|min:1',
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $kegiatan = KokurikulerKegiatan::create([
            'tahun_ajaran_id' => $tahunAktif?->id,
            'nama_kegiatan' => $data['nama_kegiatan'],
            'tema' => $data['tema'] ?? null,
            'bentuk_kegiatan' => $data['bentuk_kegiatan'],
            'koordinator_guru_id' => $data['koordinator_guru_id'],
            'semester' => $data['semester'],
        ]);

        foreach ($data['dimensi'] as $dim) {
            KokurikulerTargetDimensi::create(['kegiatan_id' => $kegiatan->id, 'nama_dimensi' => $dim]);
        }
        foreach ($data['mapel_terlibat'] as $mapelId) {
            KokurikulerMapelTerlibat::create(['kegiatan_id' => $kegiatan->id, 'mata_pelajaran_id' => $mapelId]);
        }
        foreach ($data['kelas_sasaran'] as $kr) {
            [$kelas, $rombel] = array_pad(explode('|', $kr), 2, null);
            KokurikulerKelasTerlibat::create(['kegiatan_id' => $kegiatan->id, 'kelas' => $kelas, 'rombel' => $rombel ?: null]);
        }

        return redirect()->route('erapor.kokurikuler.index')->with('success', 'Kegiatan kokurikuler berhasil dibuat.');
    }

    public function edit(KokurikulerKegiatan $kegiatan)
    {
        $kegiatan->load(['targetDimensis', 'mapelTerlibats', 'kelasTerlibats']);
        return view('erapor.kokurikuler.edit', [
            'kegiatan' => $kegiatan,
            'guruList' => Guru::orderBy('nama')->get(),
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'dimensiList' => KokurikulerKegiatan::daftarDimensi(),
            'dimensiTerpilih' => $kegiatan->targetDimensis->pluck('nama_dimensi')->toArray(),
            'mapelTerpilih' => $kegiatan->mapelTerlibats->pluck('mata_pelajaran_id')->toArray(),
            'kelasTerpilih' => $kegiatan->kelasTerlibats->map(fn ($k) => $k->rombel ? "{$k->kelas}|{$k->rombel}" : "{$k->kelas}|")->toArray(),
        ]);
    }

    public function update(Request $request, KokurikulerKegiatan $kegiatan)
    {
        $data = $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tema' => 'nullable|string|max:150',
            'bentuk_kegiatan' => 'required|in:Lintas Disiplin,G7KAIH,Khas Sekolah',
            'koordinator_guru_id' => 'required|exists:gurus,id',
            'semester' => 'required|integer|in:1,2',
            'dimensi' => 'required|array|min:1',
            'mapel_terlibat' => 'required|array|min:1',
            'kelas_sasaran' => 'required|array|min:1',
        ]);

        $kegiatan->update([
            'nama_kegiatan' => $data['nama_kegiatan'],
            'tema' => $data['tema'] ?? null,
            'bentuk_kegiatan' => $data['bentuk_kegiatan'],
            'koordinator_guru_id' => $data['koordinator_guru_id'],
            'semester' => $data['semester'],
        ]);

        $kegiatan->targetDimensis()->delete();
        foreach ($data['dimensi'] as $dim) {
            KokurikulerTargetDimensi::create(['kegiatan_id' => $kegiatan->id, 'nama_dimensi' => $dim]);
        }

        $kegiatan->mapelTerlibats()->delete();
        foreach ($data['mapel_terlibat'] as $mapelId) {
            KokurikulerMapelTerlibat::create(['kegiatan_id' => $kegiatan->id, 'mata_pelajaran_id' => $mapelId]);
        }

        $kegiatan->kelasTerlibats()->delete();
        foreach ($data['kelas_sasaran'] as $kr) {
            [$kelas, $rombel] = array_pad(explode('|', $kr), 2, null);
            KokurikulerKelasTerlibat::create(['kegiatan_id' => $kegiatan->id, 'kelas' => $kelas, 'rombel' => $rombel ?: null]);
        }

        return redirect()->route('erapor.kokurikuler.index')->with('success', 'Kegiatan kokurikuler diperbarui.');
    }

    public function destroy(KokurikulerKegiatan $kegiatan)
    {
        $kegiatan->delete();
        return back()->with('success', 'Kegiatan kokurikuler dihapus.');
    }

    // ── Input Asesmen (per siswa per dimensi) ────────────────────────
    public function pilihAsesmen(Request $request)
    {
        $user = auth()->user();
        $kegiatanId = $request->input('kegiatan');
        $kelasRombel = $request->input('kelas');

        $kegiatanQuery = KokurikulerKegiatan::query();
        if ($user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $kegiatanQuery->where('koordinator_guru_id', $guru?->id ?? 0);
        }
        $daftarKegiatan = $kegiatanQuery->orderBy('semester')->orderBy('nama_kegiatan')->get();

        $daftarKelas = collect();
        if ($kegiatanId) {
            $kegiatan = KokurikulerKegiatan::with('kelasTerlibats')->find($kegiatanId);
            $daftarKelas = $kegiatan?->kelasTerlibats ?? collect();
        }

        $siswaList = collect();
        $daftarDimensi = collect();
        $nilaiTersimpan = [];
        $catatanTersimpan = [];

        if ($kegiatanId && $kelasRombel) {
            [$kelas, $rombel] = array_pad(explode('|', $kelasRombel), 2, null);
            $daftarDimensi = KokurikulerTargetDimensi::where('kegiatan_id', $kegiatanId)->get();
            $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)->orderBy('nama_lengkap')->get();

            $asesmens = KokurikulerAsesmen::whereIn('target_dimensi_id', $daftarDimensi->pluck('id'))
                ->whereIn('siswa_id', $siswaList->pluck('id'))->get();

            foreach ($asesmens as $a) {
                $nilaiTersimpan[$a->siswa_id][$a->target_dimensi_id] = $a->nilai_kualitatif;
                $catatanTersimpan[$a->siswa_id][$a->target_dimensi_id] = $a->catatan_guru;
            }
        }

        return view('erapor.kokurikuler.input-asesmen', [
            'daftarKegiatan' => $daftarKegiatan,
            'daftarKelas' => $daftarKelas,
            'kegiatanId' => $kegiatanId,
            'kelasRombel' => $kelasRombel,
            'siswaList' => $siswaList,
            'daftarDimensi' => $daftarDimensi,
            'nilaiTersimpan' => $nilaiTersimpan,
            'catatanTersimpan' => $catatanTersimpan,
        ]);
    }

    public function saveAsesmen(Request $request)
    {
        $nilai = $request->input('nilai', []); // [siswa_id][target_dimensi_id] => nilai
        $catatan = $request->input('catatan', []);

        foreach ($nilai as $siswaId => $perDimensi) {
            foreach ($perDimensi as $dimensiId => $nk) {
                if (empty($nk)) continue;
                KokurikulerAsesmen::updateOrCreate(
                    ['target_dimensi_id' => $dimensiId, 'siswa_id' => $siswaId],
                    ['nilai_kualitatif' => $nk, 'catatan_guru' => $catatan[$siswaId][$dimensiId] ?? null]
                );
            }
        }

        return back()->with('success', 'Asesmen kokurikuler berhasil disimpan.');
    }

    /**
     * Generate deskripsi kokurikuler otomatis utk 1 siswa dari hasil asesmen
     * kegiatan tertentu - dipakai tombol "Buat Otomatis" di Kelola Rapor.
     */
    public static function generateDeskripsi(int $siswaId, int $kegiatanId): string
    {
        $kegiatan = KokurikulerKegiatan::find($kegiatanId);
        if (! $kegiatan) return '';

        $asesmens = KokurikulerAsesmen::whereHas('targetDimensi', fn ($q) => $q->where('kegiatan_id', $kegiatanId))
            ->where('siswa_id', $siswaId)->with('targetDimensi')->get();

        $unggulan = $asesmens->whereIn('nilai_kualitatif', ['Sangat Baik', 'Baik'])->pluck('targetDimensi.nama_dimensi')->map(fn ($n) => lcfirst($n));
        $perluDikembangkan = $asesmens->whereIn('nilai_kualitatif', ['Cukup', 'Kurang'])->pluck('targetDimensi.nama_dimensi')->map(fn ($n) => lcfirst($n));

        $teks = "Dalam projek '{$kegiatan->nama_kegiatan}', ananda menunjukkan perkembangan";
        if ($unggulan->isNotEmpty()) {
            $teks .= " yang sangat baik terutama pada " . $unggulan->implode(', ') . ".";
        } else {
            $teks .= ".";
        }
        if ($perluDikembangkan->isNotEmpty()) {
            $teks .= " Potensi pada " . $perluDikembangkan->implode(', ') . " perlu terus diasah.";
        }

        return $teks;
    }

    private function kelasRombelList()
    {
        return Siswa::where('status', 'aktif')
            ->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();
    }

    /** Dipanggil via AJAX dari Kelola Rapor - generate deskripsi dari asesmen sungguhan */
    public function deskripsiOtomatis(Request $request)
    {
        $request->validate(['kegiatan' => 'required|exists:kokurikuler_kegiatans,id', 'siswa' => 'required|exists:siswas,id']);
        $teks = self::generateDeskripsi((int) $request->siswa, (int) $request->kegiatan);
        return response()->json(['teks' => $teks]);
    }
}
