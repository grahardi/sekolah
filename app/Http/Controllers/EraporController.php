<?php

namespace App\Http\Controllers;

use App\Models\GuruEkstrakurikuler;
use App\Models\GuruKokurikuler;
use App\Models\GuruPengajar;
use App\Models\MataPelajaran;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\Request;

class EraporController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        return view('erapor.dashboard', [
            'tahunAktif' => $tahunAktif,
            'totalMapel' => MataPelajaran::count(),
            'totalWaliKelas' => WaliKelas::count(),
            'totalGuruPengajar' => GuruPengajar::count(),
            'totalGuruEkstrakurikuler' => GuruEkstrakurikuler::count(),
            'totalGuruKokurikuler' => GuruKokurikuler::count(),
        ]);
    }

    // ── Tahun Ajaran ────────────────────────────────────────────────────
    public function tahunAjaran()
    {
        return view('erapor.tahun-ajaran', ['tahunAjarans' => TahunAjaran::orderByDesc('nama')->get()]);
    }

    public function storeTahunAjaran(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'is_aktif' => 'nullable|boolean',
        ]);
        $data['is_aktif'] = $request->boolean('is_aktif');

        if ($data['is_aktif']) {
            TahunAjaran::query()->update(['is_aktif' => false]); // cuma 1 yg aktif
        }

        TahunAjaran::create($data);
        return back()->with('success', 'Tahun ajaran ditambahkan.');
    }

    public function aktifkanTahunAjaran(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::query()->update(['is_aktif' => false]);
        $tahunAjaran->update(['is_aktif' => true]);
        return back()->with('success', "{$tahunAjaran->label} diaktifkan.");
    }

    public function destroyTahunAjaran(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return back()->with('success', 'Tahun ajaran dihapus.');
    }

    // ── Mata Pelajaran ──────────────────────────────────────────────────
    public function mataPelajaran()
    {
        return view('erapor.mata-pelajaran', ['mapels' => MataPelajaran::orderBy('nama')->get()]);
    }

    public function storeMataPelajaran(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'kelompok' => 'nullable|string|max:50',
        ]);
        MataPelajaran::create($data);
        return back()->with('success', 'Mata pelajaran ditambahkan.');
    }

    public function destroyMataPelajaran(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return back()->with('success', 'Mata pelajaran dihapus.');
    }

    // ── Penugasan Guru (4 tab: Wali Kelas / Pengajar / Ekstrakurikuler / Kokurikuler) ──
    public function penugasan()
    {
        return view('erapor.penugasan', [
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
            'guruList' => Pegawai::orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'jabatan']),
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'waliKelas' => WaliKelas::with(['pegawai', 'tahunAjaran'])->latest()->get(),
            'guruPengajars' => GuruPengajar::with(['pegawai', 'mataPelajaran', 'tahunAjaran'])->latest()->get(),
            'guruEkstrakurikulers' => GuruEkstrakurikuler::with(['pegawai', 'tahunAjaran'])->latest()->get(),
            'guruKokurikulers' => GuruKokurikuler::with(['pegawai', 'tahunAjaran'])->latest()->get(),
        ]);
    }

    public function storeWaliKelas(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'kelas' => 'required|string|max:10',
            'rombel' => 'nullable|string|max:5',
        ]);
        WaliKelas::create($data);
        return back()->with('success', 'Wali kelas ditambahkan.');
    }

    public function destroyWaliKelas(WaliKelas $waliKelas)
    {
        $waliKelas->delete();
        return back()->with('success', 'Wali kelas dihapus.');
    }

    public function storeGuruPengajar(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string|max:10',
            'rombel' => 'nullable|string|max:5',
        ]);
        GuruPengajar::create($data);
        return back()->with('success', 'Guru pengajar ditambahkan.');
    }

    public function destroyGuruPengajar(GuruPengajar $guruPengajar)
    {
        $guruPengajar->delete();
        return back()->with('success', 'Penugasan guru pengajar dihapus.');
    }

    public function storeGuruEkstrakurikuler(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'nama_ekstrakurikuler' => 'required|string|max:100',
        ]);
        GuruEkstrakurikuler::create($data);
        return back()->with('success', 'Guru ekstrakurikuler ditambahkan.');
    }

    public function destroyGuruEkstrakurikuler(GuruEkstrakurikuler $guruEkstrakurikuler)
    {
        $guruEkstrakurikuler->delete();
        return back()->with('success', 'Guru ekstrakurikuler dihapus.');
    }

    public function storeGuruKokurikuler(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'tema_p5' => 'nullable|string|max:150',
            'kelas' => 'required|string|max:10',
            'rombel' => 'nullable|string|max:5',
        ]);
        GuruKokurikuler::create($data);
        return back()->with('success', 'Guru kokurikuler ditambahkan.');
    }

    public function destroyGuruKokurikuler(GuruKokurikuler $guruKokurikuler)
    {
        $guruKokurikuler->delete();
        return back()->with('success', 'Guru kokurikuler dihapus.');
    }

    /** Daftar kombinasi kelas-rombel yang ada di data siswa aktif (dari Buku Induk) */
    private function kelasRombelList()
    {
        return Siswa::where('status', 'aktif')
            ->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()
            ->sort()
            ->values();
    }
}
