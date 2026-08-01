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

        $tps = TujuanPembelajaran::with(['mataPelajaran', 'kelasList'])
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
        return view('erapor.tp.create', [
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
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
