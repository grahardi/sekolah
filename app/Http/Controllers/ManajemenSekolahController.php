<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ManajemenSekolahController extends Controller
{
    public function dashboard()
    {
        $hariIni = now()->toDateString();
        $totalSiswaAktif = Siswa::where('status', 'aktif')->count();
        $sudahDiabsenHariIni = AbsensiHarian::where('tanggal', $hariIni)->count();

        $rekapHariIni = AbsensiHarian::where('tanggal', $hariIni)
            ->selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');

        return view('manajemen-sekolah.dashboard', [
            'totalSiswaAktif' => $totalSiswaAktif,
            'sudahDiabsenHariIni' => $sudahDiabsenHariIni,
            'rekapHariIni' => $rekapHariIni,
            'totalGuru' => Guru::count(),
        ]);
    }

    // ── Data Siswa (baca dari data Buku Induk yang sudah ada) ───────────
    public function dataSiswa(Request $request)
    {
        $search = $request->input('search');
        $siswaList = Siswa::where('status', 'aktif')
            ->when($search, fn ($q) => $q->where('nama_lengkap', 'ilike', "%{$search}%"))
            ->orderBy('kelas')->orderBy('rombel')->orderBy('nama_lengkap')
            ->paginate(20)->withQueryString();

        return view('manajemen-sekolah.data-siswa', ['siswaList' => $siswaList, 'search' => $search]);
    }

    // ── Data Guru (baca dari data Kepegawaian/Guru E-Rapor yang sudah ada) ──
    public function dataGuru(Request $request)
    {
        $search = $request->input('search');
        $guruList = Guru::with('pegawai')
            ->when($search, fn ($q) => $q->where('nama', 'ilike', "%{$search}%"))
            ->orderBy('nama')->paginate(20)->withQueryString();

        return view('manajemen-sekolah.data-guru', ['guruList' => $guruList, 'search' => $search]);
    }

    // ── Absensi Harian (fitur baru) ──────────────────────────────────────
    public function absensiIndex(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kelasRombel = $request->input('kelas_rombel');

        $kelasList = Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();

        $siswaList = collect();
        $absensiTersimpan = [];

        if ($kelasRombel) {
            [$kelas, $rombel] = array_pad(explode('|', $kelasRombel), 2, null);
            $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->orderBy('nama_lengkap')->get();

            $absensiTersimpan = AbsensiHarian::where('tanggal', $tanggal)
                ->whereIn('siswa_id', $siswaList->pluck('id'))->get()->keyBy('siswa_id');
        }

        return view('manajemen-sekolah.absensi.index', [
            'tanggal' => $tanggal,
            'kelasList' => $kelasList,
            'kelasRombel' => $kelasRombel,
            'siswaList' => $siswaList,
            'absensiTersimpan' => $absensiTersimpan,
        ]);
    }

    public function absensiStore(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'keterangan' => 'nullable|array',
        ]);

        $guru = Guru::where('user_id', auth()->id())->first();

        foreach ($data['status'] as $siswaId => $status) {
            AbsensiHarian::updateOrCreate(
                ['siswa_id' => $siswaId, 'tanggal' => $data['tanggal']],
                [
                    'status' => $status,
                    'keterangan' => $data['keterangan'][$siswaId] ?? null,
                    'dicatat_oleh_guru_id' => $guru?->id,
                ]
            );
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    /** Rekap absensi 1 bulan per kelas - dipakai wali kelas/piket */
    public function absensiRekap(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $kelasRombel = $request->input('kelas_rombel');

        $kelasList = Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();

        $rekap = collect();
        if ($kelasRombel) {
            [$kelas, $rombel] = array_pad(explode('|', $kelasRombel), 2, null);
            $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->orderBy('nama_lengkap')->get();

            $awalBulan = \Carbon\Carbon::parse($bulan . '-01')->startOfMonth();
            $akhirBulan = $awalBulan->copy()->endOfMonth();

            $semuaAbsensi = AbsensiHarian::whereIn('siswa_id', $siswaList->pluck('id'))
                ->whereBetween('tanggal', [$awalBulan, $akhirBulan])->get()->groupBy('siswa_id');

            foreach ($siswaList as $siswa) {
                $absensiSiswa = $semuaAbsensi->get($siswa->id, collect());
                $rekap->push([
                    'siswa' => $siswa,
                    'hadir' => $absensiSiswa->where('status', 'Hadir')->count(),
                    'sakit' => $absensiSiswa->where('status', 'Sakit')->count(),
                    'izin' => $absensiSiswa->where('status', 'Izin')->count(),
                    'alpha' => $absensiSiswa->where('status', 'Alpha')->count(),
                    'dispensasi' => $absensiSiswa->where('status', 'Dispensasi')->count(),
                ]);
            }
        }

        return view('manajemen-sekolah.absensi.rekap', [
            'bulan' => $bulan,
            'kelasList' => $kelasList,
            'kelasRombel' => $kelasRombel,
            'rekap' => $rekap,
        ]);
    }
}
