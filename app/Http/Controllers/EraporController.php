<?php

namespace App\Http\Controllers;

use App\Models\GuruEkstrakurikuler;
use App\Models\Guru;
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
        $waliKelas = self::waliKelasSayaAtauNull();

        if ($waliKelas) {
            return $this->dashboardWaliKelas($waliKelas);
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $sekolahId = auth()->user()->sekolah_id;

        $totalSiswaAktif = \App\Models\Siswa::where('status', 'aktif')->count();
        $totalKelas = \App\Models\Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}-{$s->rombel}" : $s->kelas)
            ->unique()->count();

        $totalRaporSemester = 0;
        $kelengkapanRapor = 0;
        if ($tahunAktif) {
            $semesterAktif = $tahunAktif->semester === 'Genap' ? 2 : 1;
            $totalRaporSemester = \App\Models\Rapor::where('tahun_ajaran_id', $tahunAktif->id)
                ->where('semester', $semesterAktif)->count();
            $kelengkapanRapor = $totalSiswaAktif > 0 ? round(($totalRaporSemester / $totalSiswaAktif) * 100) : 0;
        }

        return view('erapor.dashboard', [
            'tahunAktif' => $tahunAktif,
            'totalMapel' => MataPelajaran::count(),
            'totalWaliKelas' => WaliKelas::count(),
            'totalGuruPengajar' => GuruPengajar::count(),
            'totalGuruEkstrakurikuler' => GuruEkstrakurikuler::count(),
            'totalGuruKokurikuler' => GuruKokurikuler::count(),
            'totalPengguna' => \App\Models\User::where('sekolah_id', $sekolahId)->count(),
            'totalSiswa' => $totalSiswaAktif,
            'totalKelas' => $totalKelas,
            'totalTp' => \App\Models\TujuanPembelajaran::count(),
            'totalPenilaian' => \App\Models\Penilaian::count(),
            'kelengkapanRapor' => $kelengkapanRapor,
            'totalRaporSemester' => $totalRaporSemester,
        ]);
    }

    /** Dashboard khusus wali kelas - fokus statistik kelasnya sendiri */
    private function dashboardWaliKelas(\App\Models\WaliKelas $waliKelas)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $semester = $tahunAktif?->semester === 'Genap' ? 2 : 1;
        $kelas = $waliKelas->kelas;
        $rombel = $waliKelas->rombel;

        $siswaList = \App\Models\Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel)->get();
        $totalSiswa = $siswaList->count();

        $raporList = $tahunAktif
            ? \App\Models\Rapor::where('tahun_ajaran_id', $tahunAktif->id)->where('semester', $semester)
                ->whereIn('siswa_id', $siswaList->pluck('id'))->get()
            : collect();

        $absensiTinggi = $raporList->filter(fn ($r) => ($r->sakit + $r->izin + $r->tanpa_keterangan) > 10)->count();
        $belumLengkap = $siswaList->count() - $raporList->filter(fn ($r) => ! empty($r->catatan_wali_kelas))->count();
        $sudahFinal = $raporList->where('status', 'Final')->count();
        $progresFinalisasi = $totalSiswa > 0 ? round(($sudahFinal / $totalSiswa) * 100) : 0;

        // Mapel yg diajar guru ini SENDIRI (kalau dia juga guru pengajar, tidak cuma wali kelas)
        $guruMengajar = GuruPengajar::where('guru_id', $waliKelas->guru_id)
            ->with('mataPelajaran')
            ->get()
            ->groupBy(fn ($p) => $p->kelas . '|' . ($p->rombel ?? ''));

        return view('erapor.dashboard-wali-kelas', [
            'waliKelas' => $waliKelas,
            'tahunAktif' => $tahunAktif,
            'kelas' => $kelas,
            'rombel' => $rombel,
            'totalSiswa' => $totalSiswa,
            'absensiTinggi' => $absensiTinggi,
            'belumLengkap' => $belumLengkap,
            'progresFinalisasi' => $progresFinalisasi,
            'sudahFinal' => $sudahFinal,
            'guruMengajar' => $guruMengajar,
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

    public function nonaktifkanTahunAjaran(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->update(['is_aktif' => false]);
        return back()->with('success', "{$tahunAjaran->label} dinonaktifkan.");
    }

    public function editTahunAjaran(TahunAjaran $tahunAjaran)
    {
        return view('erapor.tahun-ajaran-edit', ['tahunAjaran' => $tahunAjaran]);
    }

    public function updateTahunAjaran(Request $request, TahunAjaran $tahunAjaran)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        $tahunAjaran->update($data);
        return redirect()->route('erapor.tahun-ajaran')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    // ── Mata Pelajaran ──────────────────────────────────────────────────
    public function mataPelajaran(Request $request)
    {
        $search = $request->input('search');

        $mapels = MataPelajaran::when($search, fn ($q) => $q->where('nama', 'ilike', "%{$search}%"))
            ->orderBy('nama')->get();

        $mapels->each(function ($m) {
            $guruList = GuruPengajar::where('mata_pelajaran_id', $m->id)->with('guru')->get()->pluck('guru')->filter()->unique('id')->values();
            $m->guru_pengampu = $guruList;
            $m->jumlah_guru = $guruList->count();
            $m->jumlah_tp = \App\Models\TujuanPembelajaran::where('mata_pelajaran_id', $m->id)->count();
        });

        return view('erapor.mata-pelajaran', ['mapels' => $mapels, 'search' => $search]);
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
        Guru::syncFromPegawai(auth()->user()->sekolah_id);

        return view('erapor.penugasan', [
            'tahunAjarans' => TahunAjaran::orderByDesc('nama')->get(),
            'guruList' => Guru::orderBy('nama')->get(['id', 'nama', 'keterangan']),
            'mapelList' => MataPelajaran::orderBy('nama')->get(),
            'kelasList' => $this->kelasRombelList(),
            'waliKelas' => WaliKelas::with(['guru', 'tahunAjaran'])->latest()->get(),
            'guruPengajars' => GuruPengajar::with(['guru', 'mataPelajaran', 'tahunAjaran'])->latest()->get(),
            'guruEkstrakurikulers' => GuruEkstrakurikuler::with(['guru', 'tahunAjaran'])->latest()->get(),
            'guruKokurikulers' => GuruKokurikuler::with(['guru', 'tahunAjaran'])->latest()->get(),
        ]);
    }

    public function storeWaliKelas(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'guru_id' => 'required|exists:gurus,id',
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
            'guru_id' => 'required|exists:gurus,id',
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
            'guru_id' => 'required|exists:gurus,id',
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
            'guru_id' => 'required|exists:gurus,id',
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

    // ── Pengaturan Cetak Rapor ──────────────────────────────────────────
    public function pengaturanCetak()
    {
        return view('erapor.pengaturan-cetak', ['sekolah' => auth()->user()->sekolah]);
    }

    public function updatePengaturanCetak(Request $request)
    {
        $data = $request->validate([
            'rapor_ukuran_kertas' => 'required|in:A4,F4,Legal',
            'rapor_orientasi' => 'required|in:portrait,landscape',
            'rapor_font_size' => 'required|in:kecil,normal,besar',
            'rapor_tanggal_manual' => 'nullable|date',
            'rapor_tampilkan_logo' => 'nullable|boolean',
            'rapor_tampilkan_watermark' => 'nullable|boolean',
            'rapor_kota_ttd' => 'nullable|string|max:100',
            'rapor_threshold_sangat_baik' => 'required|integer|min:0|max:100',
            'rapor_threshold_baik' => 'required|integer|min:0|max:100',
            'rapor_threshold_cukup' => 'required|integer|min:0|max:100',
            'logo_kabupaten' => 'nullable|image|max:2048',
            'logo_sekolah' => 'nullable|image|max:2048',
            'watermark_rapor' => 'nullable|image|max:2048',
        ]);
        $data['rapor_tampilkan_logo'] = $request->boolean('rapor_tampilkan_logo');
        $data['rapor_tampilkan_watermark'] = $request->boolean('rapor_tampilkan_watermark');

        $sekolah = auth()->user()->sekolah;

        foreach (['logo_kabupaten', 'logo_sekolah', 'watermark_rapor'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('kop-surat', 'public');
            } else {
                unset($data[$field]); // jangan timpa yg sudah ada kalau tidak upload baru
            }
        }

        $sekolah->update($data);

        return back()->with('success', 'Pengaturan cetak rapor berhasil disimpan.');
    }

    /** Daftar kombinasi kelas-rombel yang ada di data siswa aktif (dari Buku Induk) */
    public function rekapPengajar(Request $request)
    {
        $kelasList = $this->kelasRombelList();
        $kelasDipilih = $request->input('kelas_rombel', $kelasList->first());
        $mapelList = MataPelajaran::orderBy('nama')->get();

        $rekap = collect();
        if ($kelasDipilih) {
            [$kelas, $rombel] = array_pad(explode('|', $kelasDipilih), 2, null);

            $penugasan = GuruPengajar::with('guru')
                ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
                ->get()->keyBy('mata_pelajaran_id');

            $rekap = $mapelList->map(fn ($m) => [
                'mapel' => $m->nama,
                'guru' => $penugasan->get($m->id)?->guru?->nama,
            ]);
        }

        return view('erapor.rekap-pengajar', [
            'kelasList' => $kelasList,
            'kelasDipilih' => $kelasDipilih,
            'rekap' => $rekap,
        ]);
    }

    // ── Deteksi Wali Kelas (dipakai buat pisah menu Guru biasa vs Wali Kelas) ──
    public static function waliKelasSayaAtauNull(): ?\App\Models\WaliKelas
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'guru') return null;

        $guru = \App\Models\Guru::where('user_id', $user->id)->first();
        if (! $guru) return null;

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (! $tahunAktif) return null;

        return \App\Models\WaliKelas::where('guru_id', $guru->id)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->first();
    }

    // ── Progres Penilaian (khusus Wali Kelas - pantau kelengkapan nilai kelasnya) ──
    public function progresPenilaian()
    {
        $waliKelas = self::waliKelasSayaAtauNull();
        abort_unless($waliKelas || auth()->user()->isAdmin(), 403, 'Halaman ini khusus wali kelas.');

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');

        $kelas = $waliKelas->kelas ?? null;
        $rombel = $waliKelas->rombel ?? null;
        abort_unless($kelas, 404, 'Kamu belum ditugaskan sebagai wali kelas manapun.');

        $siswaList = \App\Models\Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel)->orderBy('nama_lengkap')->get();
        $mapelIds = GuruPengajar::where('tahun_ajaran_id', $tahunAktif->id)->where('kelas', $kelas)->where('rombel', $rombel)->pluck('mata_pelajaran_id')->unique();
        $totalMapel = $mapelIds->count();

        $progres = $siswaList->map(function ($s) use ($totalMapel) {
            $sudahAda = \App\Models\RaporDetailAkademik::whereHas('rapor', fn ($q) => $q->where('siswa_id', $s->id))
                ->whereNotNull('nilai_akhir')->count();
            return [
                'siswa' => $s,
                'sudah' => $sudahAda,
                'total' => $totalMapel,
                'persen' => $totalMapel > 0 ? round(($sudahAda / $totalMapel) * 100) : 0,
            ];
        });

        return view('erapor.progres-penilaian', ['progres' => $progres, 'kelas' => $kelas, 'rombel' => $rombel]);
    }

    // ── Catatan Wali Kelas (halaman khusus, isi cepat utk semua siswa 1 kelas) ──
    public function catatanWaliIndex()
    {
        $waliKelas = self::waliKelasSayaAtauNull();
        abort_unless($waliKelas || auth()->user()->isAdmin(), 403, 'Halaman ini khusus wali kelas.');

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');

        $kelas = $waliKelas->kelas ?? null;
        $rombel = $waliKelas->rombel ?? null;
        abort_unless($kelas, 404, 'Kamu belum ditugaskan sebagai wali kelas manapun.');

        $semester = $tahunAktif->semester === 'Genap' ? 2 : 1;
        $siswaList = \App\Models\Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel)->orderBy('nama_lengkap')->get();

        foreach ($siswaList as $siswa) {
            $siswa->rapor = \App\Models\Rapor::with('detailEkskul')->firstOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $tahunAktif->id, 'semester' => $semester],
                ['kelas' => $kelas, 'rombel' => $rombel]
            );
        }

        return view('erapor.catatan-wali.index', ['siswaList' => $siswaList]);
    }

    public function catatanWaliStore(Request $request)
    {
        $data = $request->validate([
            'catatan' => 'nullable|array',
            'sakit' => 'nullable|array',
            'izin' => 'nullable|array',
            'alpa' => 'nullable|array',
        ]);

        foreach ($data['catatan'] ?? [] as $raporId => $teks) {
            $rapor = \App\Models\Rapor::find($raporId);
            if (! $rapor || $rapor->status === 'Final') continue; // lewati yg sudah final/terkunci

            $rapor->update([
                'catatan_wali_kelas' => $teks,
                'sakit' => $data['sakit'][$raporId] ?? 0,
                'izin' => $data['izin'][$raporId] ?? 0,
                'tanpa_keterangan' => $data['alpa'][$raporId] ?? 0,
            ]);
        }

        return back()->with('success', 'Catatan wali kelas berhasil disimpan.');
    }

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

    // ── Guru (roster gabungan Pegawai Kepegawaian + Guru Bantu non-Kepegawaian) ──

    public function guruIndex(Request $request)
    {
        Guru::syncFromPegawai(auth()->user()->sekolah_id);

        $search = $request->input('search');

        $gurus = Guru::with('pegawai')
            ->when($search, fn ($q) => $q->where('nama', 'ilike', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(16)
            ->withQueryString();

        $totalGuru = Guru::count();
        $totalKelasDiampu = GuruPengajar::get()
            ->map(fn ($p) => $p->kelas . '|' . ($p->rombel ?? ''))
            ->unique()->count();

        return view('erapor.guru.index', [
            'gurus' => $gurus,
            'search' => $search,
            'totalGuru' => $totalGuru,
            'totalKelasDiampu' => $totalKelasDiampu,
        ]);
    }

    public function storeGuruBantu(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'nip_nuptk' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string|max:100',
        ]);
        $data['keterangan'] = $data['keterangan'] ?: 'Guru Bantu';

        Guru::create($data);
        return back()->with('success', 'Guru bantu ditambahkan.');
    }

    public function destroyGuruBantu(Guru $guru)
    {
        abort_if($guru->isDariKepegawaian(), 403, 'Guru ini terhubung ke data Kepegawaian - kelola/hapus dari sana, bukan dari sini.');
        $guru->delete();
        return back()->with('success', 'Guru bantu dihapus.');
    }

    /**
     * Admin "login sebagai" guru ini - kalau guru belum punya akun User
     * sendiri, otomatis dibuatkan (role 'guru', username & password random).
     * Sesi admin disimpan supaya bisa kembali kapan saja.
     */
    /**
     * Format email login guru: {namadepan}{kodesekolah}.{urutguru}@guru.sekolah.co.id
     * - kodesekolah = ID sekolah di sistem (unik antar sekolah)
     * - urutguru = nomor urut guru DI SEKOLAH ITU (unik per sekolah, jadi
     *   kombinasinya otomatis unik tanpa perlu cek tabrakan nama).
     */
    /**
     * Tentukan email login guru:
     * 1. Kalau guru ini terhubung ke Pegawai DAN pegawai itu sudah punya email
     *    (dari Kepegawaian/Dapodik) - pakai itu langsung, jangan generate baru.
     * 2. Kalau tidak ada, generate: {namadepan}{kodesekolah}.{urutguru}@guru.sekolah.co.id
     *    - kalau nama depan < 5 huruf, gabung dgn nama belakang (mis. "Moch. Gibran" -> "mochgibran")
     */
    private function tentukanEmailGuru(Guru $guru, int $urutGuru): string
    {
        if ($guru->pegawai && ! empty($guru->pegawai->email)) {
            return $guru->pegawai->email;
        }

        $namaBelakang = null;
        $namaDepan = strtolower($this->ambilNamaDepanBersih($guru->nama, $namaBelakang));
        if (strlen($namaDepan) < 5 && ! empty($namaBelakang)) {
            $namaDepan .= strtolower(preg_replace('/[^a-zA-Z]/', '', $namaBelakang));
        }

        return "{$namaDepan}{$guru->sekolah_id}.{$urutGuru}@guru.sekolah.co.id";
    }

    /**
     * Bersihkan gelar di depan nama (Drs., Dra., Ir., Prof., Dr., H., Hj., dst)
     * sebelum ambil kata pertama sbg "nama depan" - gelar di BELAKANG nama
     * (S.Pd., M.Pd., Gr., dst setelah koma) otomatis aman krn cuma ambil bagian
     * sebelum koma manapun. $namaBelakang diisi lewat referensi (kata terakhir
     * sebelum koma), dipakai kalau nama depan terlalu pendek (<5 huruf).
     */
    private function ambilNamaDepanBersih(string $namaLengkap, ?string &$namaBelakang = null): string
    {
        $gelarDepan = ['drs', 'dra', 'ir', 'prof', 'dr', 'h', 'hj', 'kh', 'k.h', 'tb', 'raden', 'r'];

        $nama = trim(explode(',', $namaLengkap)[0]);
        $kataKata = array_values(array_filter(preg_split('/\s+/', trim($nama))));

        // Buang kata2 gelar depan dari awal list
        $kataKataBersih = [];
        $mulaiNama = false;
        foreach ($kataKata as $kata) {
            $bersih = strtolower(preg_replace('/[^a-zA-Z]/', '', $kata));
            if (! $mulaiNama && in_array($bersih, $gelarDepan)) continue;
            $mulaiNama = true;
            if ($bersih !== '') $kataKataBersih[] = preg_replace('/[^a-zA-Z]/', '', $kata);
        }

        if (empty($kataKataBersih)) return 'guru';

        $namaBelakang = count($kataKataBersih) > 1 ? end($kataKataBersih) : null;
        return $kataKataBersih[0];
    }

    /** Halaman Generate User massal - lihat status akun semua guru sekaligus */
    public function generateUserIndex()
    {
        Guru::syncFromPegawai(auth()->user()->sekolah_id);

        $gurus = Guru::orderBy('id')->get();
        $gurus->each(function ($g, $i) {
            $g->urutan = $i + 1;
        });

        return view('erapor.guru.generate-user', ['gurus' => $gurus]);
    }

    /** Generate akun sekaligus utk SEMUA guru yang belum punya akun */
    public function generateUserMassal()
    {
        $gurus = Guru::orderBy('id')->get();
        $hasilBaru = [];

        foreach ($gurus as $i => $guru) {
            if ($guru->user_id) continue; // sudah ada, lewati

            $urutan = $i + 1;
            $email = $this->tentukanEmailGuru($guru, $urutan);
            $password = \Illuminate\Support\Str::random(8);

            $user = \App\Models\User::create([
                'name' => $guru->nama,
                'email' => $email,
                'password' => bcrypt($password),
                'password_plain' => $password,
                'is_password_generated' => true,
                'sekolah_id' => $guru->sekolah_id,
                'role' => 'guru',
                'aktif' => true,
                'email_verified_at' => now(),
            ]);

            $guru->update(['user_id' => $user->id]);
            $hasilBaru[] = ['nama' => $guru->nama, 'email' => $email, 'password' => $password];
        }

        if (empty($hasilBaru)) {
            return back()->with('success', 'Semua guru sudah punya akun, tidak ada yang baru dibuat.');
        }

        session()->flash('akun_massal_baru', $hasilBaru);
        return back()->with('success', count($hasilBaru) . ' akun guru baru berhasil dibuat.');
    }

    public function loginSebagaiGuru(Guru $guru)
    {
        if (! $guru->user_id) {
            $urutan = Guru::where('sekolah_id', $guru->sekolah_id)->where('id', '<=', $guru->id)->count();
            $email = $this->tentukanEmailGuru($guru, $urutan);
            $passwordAsli = \Illuminate\Support\Str::random(8);

            $user = \App\Models\User::create([
                'name' => $guru->nama,
                'email' => $email,
                'password' => bcrypt($passwordAsli),
                'password_plain' => $passwordAsli,
                'is_password_generated' => true,
                'sekolah_id' => auth()->user()->sekolah_id,
                'role' => 'guru',
                'aktif' => true,
                'email_verified_at' => now(),
            ]);

            $guru->update(['user_id' => $user->id]);

            session()->flash('akun_guru_baru', [
                'email' => $email,
                'password' => $passwordAsli,
            ]);
        }

        session(['impersonating_admin_id' => auth()->id()]);
        auth()->loginUsingId($guru->user_id);

        return redirect()->route('dashboard')->with('success', "Sedang login sebagai {$guru->nama}.");
    }

    /** Kembali ke akun admin dari mode impersonate */
    public function kembaliKeAdmin()
    {
        $adminId = session('impersonating_admin_id');
        abort_unless($adminId, 404);

        session()->forget('impersonating_admin_id');
        auth()->loginUsingId($adminId);

        return redirect()->route('erapor.guru.index')->with('success', 'Kembali ke akun admin.');
    }

    /** Export User - daftar guru + akun (password kalau masih bawaan, atau catatan kalau sudah diganti) */
    public function exportUser()
    {
        $gurus = Guru::with('user')->orderBy('nama')->get();

        $rows = $gurus->map(function ($g) {
            $status = '-';
            if ($g->user) {
                if ($g->user->password_plain) {
                    $status = $g->user->password_plain;
                } elseif ($g->user->is_password_generated) {
                    $status = 'Sudah diganti user';
                } else {
                    $status = '-';
                }
            }

            return [
                'nama_guru' => $g->nama,
                'nip_nuptk' => $g->nip_nuptk,
                'email' => $g->user?->email ?? '-',
                'password' => $status,
                'status_akun' => $g->user ? 'Aktif' : 'Belum ada akun',
            ];
        });

        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download('export-user-guru-' . now()->format('Ymd') . '.xlsx');
    }

    /** Download template Excel: daftar guru + kolom Email & Password kosong utk diisi manual */
    public function downloadTemplateUser()
    {
        $gurus = Guru::orderBy('id')->get();
        $rows = $gurus->map(fn ($g) => [
            'nama_guru' => $g->nama,
            'nip_nuptk' => $g->nip_nuptk,
            'email' => $g->user?->email ?? $g->pegawai?->email ?? '',
            'password' => '',
        ]);

        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download('template-user-guru.xlsx');
    }

    /** Import User dari template terisi - cocokkan guru berdasarkan nama, buat/update akun */
    public function importUser(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $sekolahId = auth()->user()->sekolah_id;
        $imported = 0;
        $errors = [];

        (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file')->getRealPath(), function (array $row) use ($sekolahId, &$imported, &$errors) {
            $namaGuru = trim($row['nama_guru'] ?? '');
            $email = trim($row['email'] ?? '');
            $password = trim($row['password'] ?? '');

            if ($namaGuru === '' || $email === '' || $password === '') return; // baris kosong/belum diisi, lewati

            $guru = Guru::where('sekolah_id', $sekolahId)->where('nama', $namaGuru)->first();
            if (! $guru) {
                $errors[] = "Guru \"{$namaGuru}\" tidak ditemukan di data guru sekolah ini.";
                return;
            }

            $user = \App\Models\User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $guru->nama,
                    'password' => bcrypt($password),
                    'password_plain' => $password,
                    'sekolah_id' => $sekolahId,
                    'role' => 'guru',
                    'aktif' => true,
                    'email_verified_at' => now(),
                ]
            );

            $guru->update(['user_id' => $user->id]);
            $imported++;
        });

        $msg = "{$imported} akun guru berhasil di-import.";
        if (count($errors) > 0) $msg .= ' ' . count($errors) . ' baris bermasalah: ' . implode(' ', array_slice($errors, 0, 5));

        return back()->with(count($errors) > 0 ? 'warning' : 'success', $msg);
    }

    /** Dipanggil dari halaman detail Pegawai (Kepegawaian) - cari/buatkan Guru
     *  yg terhubung ke pegawai itu, lalu arahkan ke halaman Tugas Mengajar. */
    public function tugasMengajarDariPegawai(Pegawai $pegawai)
    {
        $guru = Guru::findOrCreateForPegawai($pegawai);
        return redirect()->route('erapor.guru.tugas-mengajar', $guru);
    }

    public function tugasMengajarPage(Guru $guru)
    {
        return view('erapor.guru.tugas-mengajar', ['guru' => $guru]);
    }

    /** Data lengkap semua mapel + status tiap kelas-rombel utk 1 guru tertentu */
    public function tugasMengajarData(Guru $guru)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (! $tahunAktif) {
            return response()->json(['error' => 'Belum ada tahun ajaran aktif. Atur dulu di menu Tahun Ajaran.'], 422);
        }

        $kelasList = $this->kelasRombelList();
        $mapelList = MataPelajaran::orderBy('nama')->get();

        $semuaPenugasan = GuruPengajar::with('guru')
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->get()
            ->keyBy(fn ($p) => $p->mata_pelajaran_id . '|' . $p->kelas . '|' . ($p->rombel ?? ''));

        $data = [];
        foreach ($mapelList as $mapel) {
            $kelasData = [];
            foreach ($kelasList as $k) {
                [$kelas, $rombel] = explode('|', $k);
                $key = $mapel->id . '|' . $kelas . '|' . $rombel;
                $existing = $semuaPenugasan->get($key);

                $kelasData[] = [
                    'kelas' => $kelas,
                    'rombel' => $rombel ?: null,
                    'label' => $rombel !== '' ? "{$kelas} - {$rombel}" : $kelas,
                    'assigned_to_me' => $existing && $existing->guru_id === $guru->id,
                    'assigned_to_other' => ($existing && $existing->guru_id !== $guru->id) ? $existing->guru->nama : null,
                ];
            }

            $data[] = [
                'mapel_id' => $mapel->id,
                'nama' => $mapel->nama,
                'jumlah_diampu' => collect($kelasData)->where('assigned_to_me', true)->count(),
                'kelas_list' => $kelasData,
            ];
        }

        return response()->json(['mapels' => $data, 'tahun_ajaran' => $tahunAktif->label]);
    }

    /** Toggle satu penugasan: assign kalau kosong, unassign kalau sudah milik guru ini */
    public function tugasMengajarToggle(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'required|string',
            'rombel' => 'nullable|string',
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');

        $existing = GuruPengajar::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->where('kelas', $validated['kelas'])
            ->where('rombel', $validated['rombel'] ?: null)
            ->first();

        if ($existing) {
            if ($existing->guru_id !== $guru->id) {
                return response()->json(['error' => 'Sudah diampu oleh: ' . $existing->guru->nama], 422);
            }
            $existing->delete();
            return response()->json(['status' => 'removed']);
        }

        GuruPengajar::create([
            'sekolah_id' => $guru->sekolah_id,
            'tahun_ajaran_id' => $tahunAktif->id,
            'guru_id' => $guru->id,
            'pegawai_id' => $guru->pegawai_id, // tetap dicatat kalau ada, utk kompatibilitas
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'kelas' => $validated['kelas'],
            'rombel' => $validated['rombel'] ?: null,
        ]);

        return response()->json(['status' => 'added']);
    }
}
