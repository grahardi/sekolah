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
                $arsip->{$field} = $request->file($field)->store("arsip/{$siswa->id}", 'public');
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

    public function showImportBerkas()
    {
        return view('alumni.import-berkas');
    }

    public function importBerkas(Request $request)
    {
        $jenisBerkasList = [
            'ijazah' => 'Ijazah SMP', 'sertifikat_tka' => 'Sertifikat TKA', 'transkrip_nilai' => 'Transkrip Nilai',
        ];

        $request->validate([
            'jenis' => 'required|in:' . implode(',', array_keys($jenisBerkasList)),
            'zip_file' => 'nullable|file|max:51200|mimes:zip',
            'files' => 'nullable|array',
            'files.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
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
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
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
