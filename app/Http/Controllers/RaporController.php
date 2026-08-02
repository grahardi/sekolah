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
        $user = auth()->user();
        $waliKelas = EraporController::waliKelasSayaAtauNull();
        abort_if($user->role === 'guru' && ! $waliKelas, 403, 'Halaman ini khusus wali kelas.');

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $semester = (int) $request->input('semester', $tahunAjaran?->semester === 'Genap' ? 2 : 1);

        $kelasList = $waliKelas
            ? collect([$waliKelas->rombel ? "{$waliKelas->kelas}|{$waliKelas->rombel}" : "{$waliKelas->kelas}|"])
            : $this->kelasRombelList();

        $kelasRombel = $request->input('kelas_rombel', $waliKelas ? $kelasList->first() : null);
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

        // Rapor yg statusnya sudah Final = terkunci. Nilai TIDAK dihitung ulang
        // sampai admin/wali kelas batalkan finalisasi dulu.
        if ($rapor->status === 'Final') {
            return $rapor;
        }

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
        $daftarEkskul = \App\Models\GuruEkstrakurikuler::distinct()->pluck('nama_ekstrakurikuler');
        $daftarKegiatanP5 = \App\Models\KokurikulerKegiatan::orderByDesc('id')->get();
        return view('erapor.rapor.edit', ['rapor' => $rapor, 'daftarEkskul' => $daftarEkskul, 'daftarKegiatanP5' => $daftarKegiatanP5]);
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

        // Rapor yg SUDAH Final terkunci - perubahan apapun diabaikan KECUALI
        // memang sedang membatalkan finalisasi (status dikirim balik ke Draft).
        // Ini memaksa alur 2 langkah: batalkan dulu, baru bisa edit isinya.
        if ($rapor->status === 'Final') {
            if ($data['status'] === 'Draft') {
                $rapor->update(['status' => 'Draft']);
                return redirect()->route('erapor.rapor.edit', $rapor)->with('success', 'Rapor dibuka kembali (jadi Draft). Silakan edit, lalu simpan lagi.');
            }
            return redirect()->route('erapor.rapor.edit', $rapor)->with('warning', 'Rapor ini berstatus Final (terkunci). Batalkan finalisasi dulu kalau mau mengedit.');
        }

        $rapor->update([
            'sakit' => $data['sakit'] ?? 0,
            'izin' => $data['izin'] ?? 0,
            'tanpa_keterangan' => $data['tanpa_keterangan'] ?? 0,
            'catatan_wali_kelas' => $data['catatan_wali_kelas'] ?? null,
            'deskripsi_kokurikuler' => $data['deskripsi_kokurikuler'] ?? null,
            'status' => $data['status'],
            'keterangan_kelulusan' => $rapor->semester == 2 ? ($data['keterangan_kelulusan'] ?? null) : null,
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
                    'kehadiran_hadir' => $request->input("ekskul_hadir.$i") ?: null,
                    'kehadiran_total' => $request->input("ekskul_total.$i") ?: null,
                    'evaluasi' => $request->input("ekskul_evaluasi.$i") ?: null,
                    'keterangan' => $data['ekskul_keterangan'][$i] ?? null,
                ]);
            }
        }

        return redirect()->route('erapor.rapor.edit', $rapor)->with('success', 'Rapor berhasil disimpan.');
    }

    /**
     * "Buat Otomatis" - generate teks catatan wali kelas berdasarkan mapel
     * dengan nilai tertinggi + kalimat dorongan umum, siap diedit lagi.
     */
    public function catatanOtomatis(Rapor $rapor)
    {
        $rapor->load(['siswa', 'detailAkademik.mataPelajaran']);

        $terbaik = $rapor->detailAkademik->sortByDesc('nilai_akhir')->first();
        $namaSiswa = trim(explode(' ', $rapor->siswa->nama_lengkap)[array_key_last(explode(' ', $rapor->siswa->nama_lengkap))] ?? $rapor->siswa->nama_lengkap);

        if ($terbaik) {
            $teks = "Secara keseluruhan, Ananda {$namaSiswa} telah menunjukkan perkembangan yang positif pada semester ini. "
                . "Ananda menunjukkan kompetensi yang sangat baik dalam mata pelajaran {$terbaik->mataPelajaran->nama} dengan nilai akhir {$terbaik->nilai_akhir}. "
                . "Terus pertahankan semangat belajar dan tingkatkan potensi yang dimiliki. "
                . "Dengan dukungan dari rumah dan sekolah, kami yakin Ananda {$namaSiswa} dapat meraih prestasi yang lebih gemilang.";
        } else {
            $teks = "Secara keseluruhan, Ananda {$namaSiswa} telah menunjukkan perkembangan yang positif pada semester ini. "
                . "Terus pertahankan semangat belajar dan tingkatkan potensi yang dimiliki.";
        }

        return response()->json(['teks' => $teks]);
    }

    /** Entry point Cetak Rapor khusus Wali Kelas - otomatis pilih kelasnya sendiri, gaya Cetak Massal */
    public function cetakKelas(Request $request)
    {
        $waliKelas = EraporController::waliKelasSayaAtauNull();
        abort_unless($waliKelas || auth()->user()->isAdmin(), 403);

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422, 'Belum ada tahun ajaran aktif.');

        $kelas = $waliKelas?->kelas ?? $request->input('kelas');
        $rombel = $waliKelas?->rombel ?? $request->input('rombel');
        $semester = (int) ($request->input('semester', $tahunAjaran->semester === 'Genap' ? 2 : 1));

        $siswaList = Siswa::where('status', 'aktif')->where('kelas', $kelas)->where('rombel', $rombel)
            ->orderBy('nama_lengkap')->get();

        $raporMap = Rapor::where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $semester)
            ->whereIn('siswa_id', $siswaList->pluck('id'))->get()->keyBy('siswa_id');

        return view('erapor.rapor.cetak-kelas', [
            'siswaList' => $siswaList,
            'raporMap' => $raporMap,
            'kelas' => $kelas,
            'rombel' => $rombel,
            'semester' => $semester,
        ]);
    }

    /** Finalisasi semua rapor 1 kelas sekaligus (kunci - nilai tidak berubah lagi) */
    public function finalisasiSemua(Request $request)
    {
        $request->validate(['kelas' => 'required|string', 'semester' => 'required|integer']);
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422);

        Rapor::where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $request->semester)
            ->where('kelas', $request->kelas)->where('rombel', $request->input('rombel') ?: null)
            ->update(['status' => 'Final']);

        return back()->with('success', 'Semua rapor di kelas ini sudah difinalisasi (terkunci).');
    }

    /** Batalkan finalisasi semua rapor 1 kelas sekaligus (buka kunci lagi) */
    public function batalkanFinalisasiSemua(Request $request)
    {
        $request->validate(['kelas' => 'required|string', 'semester' => 'required|integer']);
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422);

        Rapor::where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $request->semester)
            ->where('kelas', $request->kelas)->where('rombel', $request->input('rombel') ?: null)
            ->update(['status' => 'Draft']);

        return back()->with('success', 'Finalisasi seluruh rapor di kelas ini dibatalkan (kembali Draft).');
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

        // Tanggal cetak: pakai setelan manual sekolah kalau diisi, else tanggal_rapor
        // per-siswa, else hari ini.
        $tanggalCetak = $sekolah->rapor_tanggal_manual ?? $rapor->tanggal_rapor ?? now();
        $kotaTtd = $sekolah->rapor_kota_ttd ?: $sekolah->kecamatan;

        // DomPDF tidak kenal nama "F4" - ukuran itu setara dgn "folio" (~210x330mm)
        $ukuranKertas = strtolower($sekolah->rapor_ukuran_kertas) === 'f4' ? 'folio' : strtolower($sekolah->rapor_ukuran_kertas);

        $pdf = Pdf::loadView('erapor.rapor.pdf', [
            'rapor' => $rapor,
            'sekolah' => $sekolah,
            'waliKelas' => $waliKelas?->guru,
            'tanggalCetak' => $tanggalCetak,
            'kotaTtd' => $kotaTtd,
        ])->setPaper($ukuranKertas, $sekolah->rapor_orientasi);

        return $pdf->download('rapor-' . str_replace(' ', '-', $rapor->siswa->nama_lengkap) . '.pdf');
    }

    /** Download template absensi: siswa 1 kelas, kolom NISN/Nama/Kelas/S/I/A kosong, urut kelas-no induk-nama */
    public function downloadTemplateAbsensi(Request $request)
    {
        $request->validate(['kelas_rombel' => 'required|string', 'semester' => 'required|integer|in:1,2']);
        [$kelas, $rombel] = array_pad(explode('|', $request->kelas_rombel), 2, null);

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422, 'Belum ada tahun ajaran aktif.');

        $siswaList = Siswa::where('status', 'aktif')
            ->where('kelas', $kelas)->where('rombel', $rombel ?: null)
            ->orderBy('kelas')->orderBy('nis')->orderBy('nama_lengkap')
            ->get();

        $raporMap = Rapor::where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $request->semester)
            ->whereIn('siswa_id', $siswaList->pluck('id'))->get()->keyBy('siswa_id');

        $rows = $siswaList->map(function ($s) use ($raporMap) {
            $r = $raporMap->get($s->id);
            return [
                'nisn' => $s->nisn,
                'nama' => $s->nama_lengkap,
                'kelas' => $s->rombel_lengkap,
                'S' => $r->sakit ?? 0,
                'I' => $r->izin ?? 0,
                'A' => $r->tanpa_keterangan ?? 0,
            ];
        });

        $namaFile = 'template-absensi-' . str_replace(' ', '-', ($rombel ? "$kelas-$rombel" : $kelas)) . '.xlsx';
        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download($namaFile);
    }

    /** Import absensi massal dari template terisi - cocokkan berdasarkan NISN */
    public function importAbsensi(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120', 'semester' => 'required|integer|in:1,2']);

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422, 'Belum ada tahun ajaran aktif.');

        $diperbarui = 0;
        $errors = [];

        (new \Rap2hpoutre\FastExcel\FastExcel)->import($request->file('file')->getRealPath(), function (array $row) use ($tahunAjaran, $request, &$diperbarui, &$errors) {
            $nisn = trim($row['nisn'] ?? '');
            if ($nisn === '') return;

            $siswa = Siswa::where('nisn', $nisn)->where('status', 'aktif')->first();
            if (! $siswa) {
                $errors[] = "NISN {$nisn} tidak ditemukan.";
                return;
            }

            Rapor::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'semester' => $request->semester],
                [
                    'kelas' => $siswa->kelas,
                    'rombel' => $siswa->rombel,
                    'sakit' => (int) ($row['S'] ?? 0),
                    'izin' => (int) ($row['I'] ?? 0),
                    'tanpa_keterangan' => (int) ($row['A'] ?? 0),
                ]
            );
            $diperbarui++;
        });

        $msg = "Absensi {$diperbarui} siswa berhasil diperbarui.";
        if (count($errors) > 0) $msg .= ' ' . count($errors) . ' baris bermasalah: ' . implode(' ', array_slice($errors, 0, 5));

        return back()->with(count($errors) > 0 ? 'warning' : 'success', $msg);
    }

    /**
     * "Buat Otomatis" khusus Catatan UTS - cari mapel dengan nilai STS (atau
     * rata-rata TP kalau STS belum ada) tertinggi, generate kalimat serupa.
     */
    public function catatanUtsOtomatis(Rapor $rapor)
    {
        $rapor->load('siswa');
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAjaran, 422, 'Belum ada tahun ajaran aktif.');

        $mapelList = GuruPengajar::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kelas', $rapor->kelas)->where('rombel', $rapor->rombel)
            ->with('mataPelajaran')->get()->pluck('mataPelajaran')->unique('id');

        $terbaik = null;
        $skorTerbaik = -1;

        foreach ($mapelList as $mapel) {
            $sts = RaporCalculator::nilaiSts($rapor->siswa_id, $rapor->kelas, $rapor->rombel, $mapel->id, $tahunAjaran->id, $rapor->semester);
            $skor = $sts;

            if ($skor === null) {
                $perTp = RaporCalculator::nilaiPerTp($rapor->siswa_id, $rapor->kelas, $rapor->rombel, $mapel->id, $tahunAjaran->id, $rapor->semester);
                $nilaiTp = array_column($perTp, 'nilai');
                $skor = count($nilaiTp) > 0 ? array_sum($nilaiTp) / count($nilaiTp) : null;
            }

            if ($skor !== null && $skor > $skorTerbaik) {
                $skorTerbaik = $skor;
                $terbaik = $mapel;
            }
        }

        $namaSiswa = trim(collect(explode(' ', $rapor->siswa->nama_lengkap))->last());

        if ($terbaik) {
            $teks = "Ananda {$namaSiswa} menunjukkan hasil belajar yang baik pada pertengahan semester ini, "
                . "khususnya di mata pelajaran {$terbaik->nama} dengan capaian nilai " . round($skorTerbaik) . ". "
                . "Terus pertahankan semangat belajar hingga akhir semester.";
        } else {
            $teks = "Ananda {$namaSiswa} menunjukkan hasil belajar yang baik pada pertengahan semester ini. "
                . "Terus pertahankan semangat belajar hingga akhir semester.";
        }

        return response()->json(['teks' => $teks]);
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
