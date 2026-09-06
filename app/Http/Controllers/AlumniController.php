<?php

namespace App\Http\Controllers;

use App\Imports\DapodikImport;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'tahun_masuk']);

        $alumni = Siswa::where('status', 'lulus')
            ->when($filters['search'] ?? null, fn ($q, $v) => $q->where('nama_lengkap', 'ilike', "%{$v}%"))
            ->when($filters['tahun_masuk'] ?? null, fn ($q, $v) => $q->where('tahun_masuk', $v))
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        $tahunList = Siswa::where('status', 'lulus')->select('tahun_masuk')->distinct()->orderByDesc('tahun_masuk')->pluck('tahun_masuk');

        return view('alumni.index', compact('alumni', 'filters', 'tahunList'));
    }

    public function create()
    {
        return view('alumni.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|unique:siswas,nisn',
            'nis' => 'nullable|string',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'kelas' => 'nullable|string',
            'tahun_masuk' => 'nullable|integer',
            'tahun_lulus' => 'nullable|integer',
        ]);

        $validated['status'] = 'lulus';

        Siswa::create($validated);

        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function showImportDapodik()
    {
        return view('alumni.import-dapodik');
    }

    public function importDapodik(Request $request)
    {
        $tahunSekarang = (int) date('Y');
        $request->validate([
            'file_dapodik' => 'required|mimes:xlsx,xls|max:10240',
            'tahun_lulus' => 'required|integer|min:' . ($tahunSekarang - 5) . '|max:' . $tahunSekarang,
        ]);

        $filePath = $request->file('file_dapodik')->getRealPath();
        $import = new DapodikImport('lulus', (int) $request->tahun_lulus);
        $import->import($filePath);

        $pesan = "{$import->getImportedCount()} data alumni berhasil diimport";
        if ($import->getSkippedCount() > 0) {
            $pesan .= ", {$import->getSkippedCount()} baris dilewati";
        }

        if (! empty($import->getErrors())) {
            return back()->withErrors($import->getErrors());
        }

        return redirect()->route('alumni.index')->with('success', $pesan . '.');
    }

    /** Proteksi berlapis - pastikan siswa yg diakses BENAR alumni (status=lulus), gak numpang ke data siswa aktif */
    private function pastikanAlumni(Siswa $siswa): void
    {
        abort_unless($siswa->status === 'lulus', 404, 'Data ini bukan alumni.');
    }

    public function arsip(Siswa $siswa)
    {
        $this->pastikanAlumni($siswa);
        $arsip = $siswa->arsipBerkas ?? new \App\Models\ArsipBerkas(['siswa_id' => $siswa->id]);
        $siswa->load('alumniAjuanUlang.sekolahTujuan');
        return view('alumni.arsip', compact('siswa', 'arsip'));
    }

    public function arsipUpdate(Request $request, Siswa $siswa)
    {
        $this->pastikanAlumni($siswa);

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $arsip = $siswa->arsipBerkas ?? new \App\Models\ArsipBerkas(['siswa_id' => $siswa->id]);

        foreach (\App\Models\ArsipBerkas::berkasAktif() + \App\Models\ArsipBerkas::berkasLulus() as $field => $meta) {
            if ($request->hasFile($field)) {
                if ($arsip->{$field}) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($arsip->{$field});
                }
                $path = $request->file($field)->store("arsip/{$siswa->id}", 'public');
                $arsip->{$field} = $path;

                // Field 'foto' di Berkas Masuk ini yg dipakai sbg foto profil
                // resmi siswa (tampil di list Alumni, kartu, cetak, dll) -
                // sinkronkan ke siswas.foto biar gak ada 2 sumber foto beda.
                if ($field === 'foto') {
                    if ($siswa->foto) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
                    }
                    $siswa->update(['foto' => $path]);
                }
            }
        }

        if ($request->filled('catatan')) {
            $arsip->catatan = $request->catatan;
        }

        $arsip->siswa_id = $siswa->id;
        $arsip->save();

        return back()->with('success', 'Berkas alumni berhasil disimpan.');
    }

    public function arsipHapus(Request $request, Siswa $siswa)
    {
        $this->pastikanAlumni($siswa);

        $request->validate(['field' => 'required|string']);

        $arsip = $siswa->arsipBerkas;
        abort_unless($arsip, 404);

        $fieldValid = array_key_exists($request->field, \App\Models\ArsipBerkas::berkasAktif() + \App\Models\ArsipBerkas::berkasLulus());
        abort_unless($fieldValid, 422, 'Field tidak valid.');

        if ($arsip->{$request->field}) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($arsip->{$request->field});
            $arsip->{$request->field} = null;
            $arsip->save();
        }

        return back()->with('success', 'Berkas berhasil dihapus.');
    }

    public function showImportNomorIjazah()
    {
        return view('alumni.import-nomor-ijazah');
    }

    public function importNomorIjazah(Request $request)
    {
        $request->validate(['file_ijazah' => 'required|mimes:xlsx,xls|max:10240']);

        $filePath = $request->file('file_ijazah')->getRealPath();
        $import = new \App\Imports\ImportNomorIjazah();
        $import->import($filePath);

        if (! empty($import->getErrors())) {
            return back()->withErrors($import->getErrors());
        }

        $pesan = "{$import->getUpdatedCount()} nomor ijazah berhasil diperbarui";
        if ($import->getSkippedCount() > 0) {
            $pesan .= ", {$import->getSkippedCount()} baris dilewati (lihat detail di bawah)";
        }

        return back()->with('success', $pesan . '.')->with('warnings_ijazah', $import->getWarnings());
    }

    public function historyIndex(Request $request)
    {
        $tahunLulus = $request->input('tahun_lulus');
        $kategoriFilter = $request->input('kategori');
        $statusFilter = $request->input('status_isi');
        $search = $request->input('search');

        $baseQuery = Siswa::where('status', 'lulus')
            ->when($tahunLulus, fn ($q, $v) => $q->where('tahun_lulus', $v));

        $total = (clone $baseQuery)->count();
        $sudahMengisi = (clone $baseQuery)->whereNotNull('alumni_diisi_at')->count();
        $lanjutSekolah = (clone $baseQuery)->where('alumni_kategori', 'lanjut_sekolah')->count();
        $pondokPesantren = (clone $baseQuery)->where('alumni_kategori', 'pondok_pesantren')->count();
        $bekerja = (clone $baseQuery)->where('alumni_kategori', 'bekerja')->count();
        $tidakMelanjutkan = (clone $baseQuery)->where('alumni_kategori', 'tidak_melanjutkan')->count();
        $lainnya = (clone $baseQuery)->where('alumni_kategori', 'lainnya')->count();

        $persenMengisi = $total > 0 ? round($sudahMengisi / $total * 100) : 0;
        // Persentase kategori (lanjut/bekerja/dll) dihitung dari yg SUDAH
        // MENGISI, bukan dari total alumni - lebih valid krn yg belum isi
        // gak seharusnya ikut "menurunkan" persentase kategori tertentu.
        $persenLanjut = $sudahMengisi > 0 ? round($lanjutSekolah / $sudahMengisi * 100) : 0;
        $persenPondok = $sudahMengisi > 0 ? round($pondokPesantren / $sudahMengisi * 100) : 0;
        $persenBekerja = $sudahMengisi > 0 ? round($bekerja / $sudahMengisi * 100) : 0;
        $persenTidakMelanjutkan = $sudahMengisi > 0 ? round($tidakMelanjutkan / $sudahMengisi * 100) : 0;
        $persenLainnya = $sudahMengisi > 0 ? round($lainnya / $sudahMengisi * 100) : 0;

        $sekolahFavorit = (clone $baseQuery)->where('alumni_kategori', 'lanjut_sekolah')
            ->leftJoin('sekolah_tujuan', 'siswas.alumni_sekolah_tujuan_id', '=', 'sekolah_tujuan.id')
            ->selectRaw("COALESCE(sekolah_tujuan.nama_sekolah, siswas.alumni_sekolah_tujuan_manual) as nama, COUNT(*) as jumlah")
            ->whereNotNull('siswas.alumni_diisi_at')
            ->groupBy('nama')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        $jurusanFavorit = (clone $baseQuery)->where('alumni_kategori', 'lanjut_sekolah')
            ->whereNotNull('alumni_jurusan')
            ->where('alumni_jurusan', '!=', '')
            ->select('alumni_jurusan')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('alumni_jurusan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        // Filter & urutkan list alumni sesuai pilihan kategori/status pengisian
        $listQuery = (clone $baseQuery)
            ->when($kategoriFilter, fn ($q, $v) => $q->where('alumni_kategori', $v))
            ->when($statusFilter === 'sudah', fn ($q) => $q->whereNotNull('alumni_diisi_at'))
            ->when($statusFilter === 'belum', fn ($q) => $q->whereNull('alumni_diisi_at'))
            ->when($search, fn ($q, $v) => $q->where(fn ($qq) => $qq->where('nama_lengkap', 'ilike', "%{$v}%")->orWhere('nisn', 'ilike', "%{$v}%")->orWhere('nis', 'ilike', "%{$v}%")));

        $daftarAlumni = $listQuery->orderByDesc('alumni_diisi_at')->paginate(20)->withQueryString();

        $tahunList = Siswa::where('status', 'lulus')->whereNotNull('tahun_lulus')->select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');

        $npsn = auth()->user()->sekolah->npsn;
        $sekolahTujuanList = \App\Models\SekolahTujuan::where('aktif', true)->orderBy('urutan')->orderBy('nama_sekolah')->get();

        return view('alumni.history.index', compact(
            'total', 'sudahMengisi', 'lanjutSekolah', 'pondokPesantren', 'bekerja', 'tidakMelanjutkan', 'lainnya',
            'persenMengisi', 'persenLanjut', 'persenPondok', 'persenBekerja', 'persenTidakMelanjutkan', 'persenLainnya',
            'sekolahFavorit', 'jurusanFavorit', 'daftarAlumni', 'tahunList', 'tahunLulus', 'npsn',
            'kategoriFilter', 'statusFilter', 'sekolahTujuanList', 'search'
        ));
    }

    public function ajuanUlangIndex()
    {
        $daftar = \App\Models\AlumniAjuanUlang::with('siswa', 'sekolahTujuan')
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('alumni.ajuan-ulang.index', compact('daftar'));
    }

    public function ajuanUlangProses(Request $request, \App\Models\AlumniAjuanUlang $ajuan)
    {
        $request->validate(['aksi' => 'required|in:setuju,tolak']);

        if ($ajuan->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        if ($request->aksi === 'setuju') {
            $ajuan->siswa->update([
                'alumni_kategori' => $ajuan->alumni_kategori,
                'alumni_sekolah_tujuan_id' => $ajuan->alumni_sekolah_tujuan_id,
                'alumni_sekolah_tujuan_manual' => $ajuan->alumni_sekolah_tujuan_manual,
                'alumni_jurusan' => $ajuan->alumni_jurusan,
                'alumni_keterangan' => $ajuan->alumni_keterangan,
                'alumni_diisi_at' => now(),
            ]);
        }

        $ajuan->update([
            'status' => $request->aksi === 'setuju' ? 'disetujui' : 'ditolak',
            'diproses_oleh_user_id' => auth()->id(),
            'diproses_at' => now(),
        ]);

        return back()->with('success', $request->aksi === 'setuju' ? 'Pengajuan disetujui & data alumni diperbarui.' : 'Pengajuan ditolak.');
    }

    public function historyEditForm(Siswa $siswa)
    {
        abort_unless($siswa->status === 'lulus', 404);

        $sekolahTujuanList = \App\Models\SekolahTujuan::where('aktif', true)->orderBy('urutan')->orderBy('nama_sekolah')->get();

        return view('alumni.history.edit', compact('siswa', 'sekolahTujuanList'));
    }

    public function historyEdit(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'alumni_kategori' => 'required|in:lanjut_sekolah,pondok_pesantren,tidak_melanjutkan,bekerja,lainnya',
            'alumni_sekolah_tujuan_id' => 'nullable|exists:sekolah_tujuan,id',
            'alumni_sekolah_tujuan_manual' => 'nullable|string|max:150',
            'alumni_pondok_manual' => 'nullable|string|max:150',
            'alumni_jurusan' => 'nullable|string|max:100',
            'alumni_keterangan' => 'nullable|string|max:255',
        ]);

        // Field manual sekolah vs pondok DIPISAH namanya di form (hindari
        // tabrakan submit krn keduanya ada di DOM meski satu disembunyikan),
        // gabungkan lagi sesuai kategori yg dipilih.
        $manualTujuan = $data['alumni_kategori'] === 'pondok_pesantren'
            ? ($data['alumni_pondok_manual'] ?? null)
            : ($data['alumni_sekolah_tujuan_manual'] ?? null);

        $update = [
            'alumni_kategori' => $data['alumni_kategori'],
            'alumni_sekolah_tujuan_id' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_sekolah_tujuan_id'] ?? null) : null,
            'alumni_sekolah_tujuan_manual' => in_array($data['alumni_kategori'], ['lanjut_sekolah', 'pondok_pesantren']) ? $manualTujuan : null,
            'alumni_jurusan' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_jurusan'] ?? null) : null,
            'alumni_keterangan' => in_array($data['alumni_kategori'], ['bekerja', 'tidak_melanjutkan', 'lainnya']) ? ($data['alumni_keterangan'] ?? null) : null,
            'alumni_diisi_at' => now(),
        ];

        $siswa->update($update);

        // Catat jg sbg riwayat (auto disetujui, admin yg edit langsung gak
        // perlu approval diri sendiri) - biar Riwayat Alumni tetap konsisten
        \App\Models\AlumniAjuanUlang::create(array_merge(
            ['siswa_id' => $siswa->id],
            array_diff_key($update, ['alumni_diisi_at' => null]),
            ['status' => 'disetujui', 'diproses_oleh_user_id' => auth()->id(), 'diproses_at' => now()]
        ));

        return redirect()->route('alumni.history.edit.form', $siswa)->with('success', "Data alumni {$siswa->nama_lengkap} berhasil diperbarui.");
    }

    public function sekolahTujuanIndex()
    {
        $daftar = \App\Models\SekolahTujuan::orderBy('urutan')->orderBy('nama_sekolah')->get();

        return view('alumni.sekolah-tujuan.index', compact('daftar'));
    }

    public function sekolahTujuanStore(Request $request)
    {
        $data = $request->validate([
            'nama_sekolah' => 'required|string|max:150',
            'jenjang' => 'nullable|string|max:20',
        ]);

        $data['sekolah_id'] = auth()->user()->sekolah_id;

        \App\Models\SekolahTujuan::create($data);

        return back()->with('success', 'Sekolah tujuan berhasil ditambahkan.');
    }

    public function sekolahTujuanUpdate(Request $request, \App\Models\SekolahTujuan $sekolahTujuan)
    {
        $data = $request->validate([
            'nama_sekolah' => 'required|string|max:150',
            'jenjang' => 'nullable|string|max:20',
            'aktif' => 'nullable|boolean',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        $sekolahTujuan->update($data);

        return back()->with('success', 'Sekolah tujuan berhasil diperbarui.');
    }

    public function sekolahTujuanDestroy(\App\Models\SekolahTujuan $sekolahTujuan)
    {
        $sekolahTujuan->delete();

        return back()->with('success', 'Sekolah tujuan berhasil dihapus.');
    }

    public function showImportBerkas()
    {
        return view('alumni.import-berkas');
    }

    public function importBerkas(Request $request)
    {
        $jenisBerkasList = [
            'foto' => 'Foto Siswa', 'ijazah' => 'Ijazah SMP', 'sertifikat_tka' => 'Sertifikat TKA', 'transkrip_nilai' => 'Transkrip Nilai',
        ];

        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys($jenisBerkasList)),
            'zip_file' => 'nullable|file|max:51200|mimes:zip',
            'files' => 'nullable|array',
            'files.*' => $request->input('jenis') === 'foto' ? 'file|max:5120|mimes:jpg,jpeg,png' : 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        if (! $request->hasFile('zip_file') && empty($request->file('files'))) {
            return back()->with('error', 'Pilih file ZIP atau file satuan dulu.');
        }

        $jenis = $request->input('jenis');
        $imported = 0;
        $errors = [];
        $folderSementara = null;

        // Kumpulkan daftar file yg mau diproses - baik dari ZIP maupun upload biasa,
        // logikanya SAMA persis setelah ini (cocokkan nama file ke NIS/NISN).
        $daftarFile = []; // [nama_asli => path_fisik_di_disk]

        if ($request->hasFile('zip_file')) {
            $folderSementara = storage_path('app/temp-extract-' . uniqid());
            mkdir($folderSementara, 0755, true);

            $zip = new \ZipArchive();
            if ($zip->open($request->file('zip_file')->getRealPath()) === true) {
                $zip->extractTo($folderSementara);
                $zip->close();

                // Ambil semua file hasil extract (rekursif, jaga2 ada subfolder di dalam ZIP)
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderSementara));
                foreach ($iterator as $item) {
                    if ($item->isFile() && ! str_starts_with($item->getFilename(), '.')) {
                        $ext = strtolower($item->getExtension());
                        $extValid = $jenis === 'foto' ? ['jpg', 'jpeg', 'png'] : ['jpg', 'jpeg', 'png', 'pdf'];
                        if (in_array($ext, $extValid)) {
                            $daftarFile[$item->getFilename()] = $item->getPathname();
                        }
                    }
                }
            } else {
                return back()->with('error', 'Gagal membuka file ZIP - pastikan file tidak rusak.');
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $daftarFile[$file->getClientOriginalName()] = $file->getRealPath();
            }
        }

        foreach ($daftarFile as $namaAsli => $pathFisik) {
            $identifier = trim(pathinfo($namaAsli, PATHINFO_FILENAME));

            // HANYA cari di siswa dgn status LULUS - gak akan pernah nyentuh siswa aktif
            $siswa = Siswa::where('status', 'lulus')
                ->where(fn ($q) => $q->where('nisn', $identifier)->orWhere('nis', $identifier))
                ->first();

            if (! $siswa) {
                $errors[] = "File \"{$namaAsli}\": tidak ada ALUMNI dengan NIS/NISN \"{$identifier}\".";
                continue;
            }

            $ekstensi = pathinfo($namaAsli, PATHINFO_EXTENSION);

            if ($jenis === 'foto') {
                $pathTujuan = "arsip/{$siswa->id}/foto." . $ekstensi;
                \Illuminate\Support\Facades\Storage::disk('public')->put($pathTujuan, file_get_contents($pathFisik));

                $arsip = $siswa->arsipBerkas ?? new \App\Models\ArsipBerkas(['siswa_id' => $siswa->id]);
                if ($arsip->foto && $arsip->foto !== $pathTujuan) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($arsip->foto);
                }
                $arsip->foto = $pathTujuan;
                $arsip->siswa_id = $siswa->id;
                $arsip->save();

                // Sinkronkan ke siswas.foto jg - itu yg dipakai list Alumni/kartu/cetak
                if ($siswa->foto && $siswa->foto !== $pathTujuan) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->foto);
                }
                $siswa->update(['foto' => $pathTujuan]);

                $imported++;
                continue;
            }

            $pathTujuan = "arsip/{$siswa->id}/{$jenis}." . $ekstensi;
            \Illuminate\Support\Facades\Storage::disk('public')->put($pathTujuan, file_get_contents($pathFisik));

            $arsip = $siswa->arsipBerkas ?? new \App\Models\ArsipBerkas(['siswa_id' => $siswa->id]);
            if ($arsip->{$jenis} && $arsip->{$jenis} !== $pathTujuan) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($arsip->{$jenis});
            }
            $arsip->{$jenis} = $pathTujuan;
            $arsip->siswa_id = $siswa->id;
            $arsip->save();

            $imported++;
        }

        // Bersihkan folder sementara hasil extract ZIP
        if ($folderSementara && is_dir($folderSementara)) {
            $this->hapusFolder($folderSementara);
        }

        $pesan = "{$imported} berkas {$jenisBerkasList[$jenis]} berhasil diupload.";
        if (! empty($errors)) {
            return back()->withErrors($errors)->with('success', $imported > 0 ? $pesan : null);
        }

        return back()->with('success', $pesan);
    }

    private function hapusFolder(string $folder): void
    {
        $items = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($folder);
    }
}
