<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TpKelas;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;

class TujuanPembelajaranController extends Controller
{
    public function index(Request $request)
    {
        $mapelId = $request->input('mapel_id');
        $user = auth()->user();

        $query = TujuanPembelajaran::with(['mataPelajaran', 'kelasList']);

        // Guru cuma lihat TP yg cocok dgn mapel & kelas yg benar-benar dia ajar
        if ($user->role === 'guru') {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
            $penugasan = $guru
                ? \App\Models\GuruPengajar::where('guru_id', $guru->id)->get(['mata_pelajaran_id', 'kelas', 'rombel'])
                : collect();

            if ($penugasan->isEmpty()) {
                $query->whereRaw('1 = 0'); // belum ada penugasan mengajar sama sekali
            } else {
                $mapelIds = $penugasan->pluck('mata_pelajaran_id')->unique();
                $kelasRombelSaya = $penugasan->map(fn ($p) => $p->kelas . '|' . ($p->rombel ?? ''))->unique();

                $query->whereIn('mata_pelajaran_id', $mapelIds)
                    ->whereHas('kelasList', function ($q) use ($kelasRombelSaya) {
                        $q->where(function ($qq) use ($kelasRombelSaya) {
                            foreach ($kelasRombelSaya as $kr) {
                                [$kelas, $rombel] = explode('|', $kr);
                                $qq->orWhere(function ($q3) use ($kelas, $rombel) {
                                    $q3->where('kelas', $kelas)->where('rombel', $rombel ?: null);
                                });
                            }
                        });
                    });
            }
        }

        $tps = $query
            ->when($mapelId, fn ($q) => $q->where('mata_pelajaran_id', $mapelId))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('erapor.tp.index', [
            'tps' => $tps,
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'filterMapel' => $mapelId,
        ]);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'guru') {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
            $penugasan = $guru
                ? \App\Models\GuruPengajar::with('mataPelajaran')->where('guru_id', $guru->id)->get()
                : collect();

            return view('erapor.tp.create', [
                'mapelList' => $penugasan->pluck('mataPelajaran')->unique('id')->values(),
                'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
                'kelasList' => $penugasan->map(fn ($p) => $p->rombel ? "{$p->kelas}|{$p->rombel}" : "{$p->kelas}|")->unique()->values(),
                'penugasanSaya' => $penugasan, // dipakai buat batasi kombinasi mapel+kelas di JS
            ]);
        }

        return view('erapor.tp.create', [
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'penugasanSaya' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'fase' => 'nullable|string|max:5',
            'kode_tp' => 'nullable|string|max:20',
            'deskripsi_tp' => 'required|string',
            'semester' => 'required|integer|in:1,2',
            'kelas_rombel' => 'required|array|min:1',
        ]);

        // Kalau yg buat guru, pastikan mapel & SEMUA kelas yg dipilih memang
        // ada di penugasan mengajarnya - jangan cuma percaya UI, guru nakal
        // bisa saja kirim request manual.
        $user = auth()->user();
        if ($user->role === 'guru') {
            $guru = \App\Models\Guru::where('user_id', $user->id)->first();
            $kombinasiValid = \App\Models\GuruPengajar::where('guru_id', $guru?->id ?? 0)
                ->where('mata_pelajaran_id', $data['mata_pelajaran_id'])
                ->get()
                ->map(fn ($p) => $p->rombel ? "{$p->kelas}|{$p->rombel}" : "{$p->kelas}|")
                ->unique();

            foreach ($data['kelas_rombel'] as $kr) {
                abort_unless($kombinasiValid->contains($kr), 403, 'Kamu tidak ditugaskan mengajar mapel/kelas ini.');
            }
        }

        $tp = TujuanPembelajaran::create([
            'mata_pelajaran_id' => $data['mata_pelajaran_id'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
            'fase' => $data['fase'] ?? null,
            'kode_tp' => $data['kode_tp'] ?? null,
            'deskripsi_tp' => $data['deskripsi_tp'],
            'semester' => $data['semester'],
        ]);

        foreach ($data['kelas_rombel'] as $kr) {
            [$kelas, $rombel] = array_pad(explode('|', $kr), 2, null);
            TpKelas::create(['tujuan_pembelajaran_id' => $tp->id, 'kelas' => $kelas, 'rombel' => $rombel ?: null]);
        }

        return redirect()->route('erapor.tp.index')->with('success', 'Tujuan Pembelajaran ditambahkan.');
    }

    public function destroy(TujuanPembelajaran $tp)
    {
        $tp->delete();
        return back()->with('success', 'Tujuan Pembelajaran dihapus.');
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
