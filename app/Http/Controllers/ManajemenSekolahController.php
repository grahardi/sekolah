<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ManajemenSekolahController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('manajemen-sekolah.dashboard');
        }
        return view('manajemen-sekolah.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! auth()->attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('ingat'))) {
            return back()->withErrors(['email' => 'Email/Nomor ID atau password salah.'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('manajemen-sekolah.dashboard'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('manajemen-sekolah.login');
    }

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

    // ── Tata Tertib (Pelanggaran Siswa) ──────────────────────────────────
    public function tatibIndex(Request $request)
    {
        $search = $request->input('search');
        $pelanggaranList = \App\Models\PelanggaranSiswa::with(['siswa', 'pelapor'])
            ->when($search, fn ($q) => $q->whereHas('siswa', fn ($s) => $s->where('nama_lengkap', 'ilike', "%{$search}%")))
            ->orderByDesc('tanggal')->paginate(20)->withQueryString();

        $rekapPoin = \App\Models\PelanggaranSiswa::selectRaw('siswa_id, sum(poin) as total_poin')
            ->groupBy('siswa_id')->orderByDesc('total_poin')->limit(10)
            ->with('siswa')->get();

        return view('manajemen-sekolah.tatib.index', ['pelanggaranList' => $pelanggaranList, 'search' => $search, 'rekapPoin' => $rekapPoin]);
    }

    public function tatibCreate()
    {
        $siswaList = Siswa::where('status', 'aktif')->orderBy('kelas')->orderBy('nama_lengkap')->get();
        return view('manajemen-sekolah.tatib.create', [
            'siswaList' => $siswaList,
            'daftarKategori' => \App\Models\PelanggaranSiswa::daftarKategori(),
        ]);
    }

    public function tatibStore(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'kategori' => 'required|string',
            'poin' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $guru = Guru::where('user_id', auth()->id())->first();
        \App\Models\PelanggaranSiswa::create($data + ['dilaporkan_oleh_guru_id' => $guru?->id]);

        return redirect()->route('manajemen-sekolah.tatib.index')->with('success', 'Pelanggaran berhasil dicatat.');
    }

    public function tatibTindakLanjut(Request $request, \App\Models\PelanggaranSiswa $pelanggaran)
    {
        $data = $request->validate(['tindak_lanjut' => 'required|string']);
        $pelanggaran->update(['tindak_lanjut' => $data['tindak_lanjut'], 'status' => 'Sudah Ditindak']);

        return back()->with('success', 'Tindak lanjut berhasil disimpan.');
    }

    public function tatibDestroy(\App\Models\PelanggaranSiswa $pelanggaran)
    {
        $pelanggaran->delete();
        return back()->with('success', 'Catatan pelanggaran dihapus.');
    }

    /** Menu Piket - landing page khusus guru dgn flag is_piket (atau admin) */
    public function menuPiket()
    {
        $user = auth()->user();
        $guruSaya = Guru::where('user_id', $user->id)->first();
        abort_unless($user->isAdmin() || ($guruSaya && $guruSaya->is_piket), 403, 'Halaman ini khusus Piket.');

        return view('manajemen-sekolah.menu-piket');
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

    public function updateRoleGuru(Request $request, Guru $guru)
    {
        $data = [];
        foreach (['is_piket', 'is_tatib', 'is_bk', 'is_kebersihan', 'is_keagamaan', 'is_kepsek', 'is_kesiswaan'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        $guru->update($data);

        return back()->with('success', "Role {$guru->nama} berhasil diperbarui.");
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
