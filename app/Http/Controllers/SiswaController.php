<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use App\Imports\DapodikImport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search','kelas_rombel','status','tingkat','tahun_masuk']);

        $siswas = Siswa::filter($filters)
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $kelasRombelList = Siswa::whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();
        $tingkatList  = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $tahunList    = Siswa::select('tahun_masuk')->distinct()->orderByDesc('tahun_masuk')->pluck('tahun_masuk');
        $totalSiswa   = Siswa::count();
        $totalAktif   = Siswa::where('status','aktif')->count();
        $totalLaki    = Siswa::where('jenis_kelamin','L')->count();
        $totalPerempu = Siswa::where('jenis_kelamin','P')->count();

        $rekapKelas = Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->select('kelas', 'rombel',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when jenis_kelamin='L' then 1 else 0 end) as laki"),
                DB::raw("sum(case when jenis_kelamin='P' then 1 else 0 end) as perempuan"))
            ->groupBy('kelas', 'rombel')->orderBy('kelas')->orderBy('rombel')->get();

        return view('siswa.index', compact(
            'siswas','filters','kelasRombelList','tingkatList','tahunList',
            'totalSiswa','totalAktif','totalLaki','totalPerempu','rekapKelas'
        ));
    }

    public function create() { return view('siswa.create'); }

    public function store(Request $request)
    {
        $validated = $this->validateSiswa($request);
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa/foto','public');
        }
        $siswa = Siswa::create($validated);
        return redirect()->route('siswa.show', $siswa)->with('success','Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load(['nilaiRapors','nilaiP5s','nilaiEkskuls','kehadirans','riwayatKelas']);
        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa) { return view('siswa.edit', compact('siswa')); }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $this->validateSiswa($request, $siswa->id);
        if ($request->hasFile('foto')) {
            if ($siswa->foto) Storage::disk('public')->delete($siswa->foto);
            $validated['foto'] = $request->file('foto')->store('siswa/foto','public');
        }
        $siswa->update($validated);
        return redirect()->route('siswa.show', $siswa)->with('success','Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success','Data siswa berhasil dihapus.');
    }

    // ── Export ────────────────────────────────────────────────────────────────
    public function exportChoice(Request $request)
    {
        return view('siswa.export', ['query' => $request->only(['search','kelas_rombel','status','tingkat','tahun_masuk'])]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search','kelas_rombel','status','tingkat','tahun_masuk']);
        return (new SiswaExport($filters))->download('buku-induk-siswa-'.now()->format('Ymd').'.xlsx');
    }

    public function exportPdfAll(Request $request)
    {
        // Render PDF untuk ratusan siswa sekaligus cukup berat bagi DomPDF -
        // naikkan batas memori khusus untuk request ini saja (tidak
        // mempengaruhi request lain), supaya tidak fatal error kehabisan memori.
        ini_set('memory_limit', '512M');

        $filters = $request->only(['search','kelas_rombel','status','tingkat','tahun_masuk']);
        $siswas  = Siswa::filter($filters)->orderBy('nama_lengkap')->get();
        return Pdf::loadView('siswa.pdf-list', compact('siswas'))
            ->setPaper('a4','landscape')
            ->download('daftar-siswa-'.now()->format('Ymd').'.pdf');
    }

    public function cetakBukuInduk(Siswa $siswa)
    {
        $siswa->load(['nilaiRapors','nilaiP5s','nilaiEkskuls','kehadirans','riwayatKelas','prestasis']);

        $pdf = Pdf::loadView('siswa.pdf-buku-induk', compact('siswa'));
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('margin_top',    0);
        $pdf->getDomPDF()->set_option('margin_bottom', 0);
        $pdf->getDomPDF()->set_option('margin_left',   0);
        $pdf->getDomPDF()->set_option('margin_right',  0);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('buku-induk-'.$siswa->nisn.'.pdf');
    }

    public function cetakKartu(Siswa $siswa)
    {
        return Pdf::loadView('siswa.pdf-kartu', compact('siswa'))
            ->setPaper('a5','portrait')
            ->download('kartu-siswa-'.$siswa->nisn.'.pdf');
    }

    // ── Import ────────────────────────────────────────────────────────────────
    public function showImport() { return view('siswa.import'); }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $filePath = $request->file('file')->getRealPath();
        $import   = new SiswaImport();
        $import->import($filePath);

        return $this->handleImportResult($import->getErrors(), $import->getWarnings(), $import->getImportedCount(), $import->getSkippedCount(), '');
    }

    public function downloadTemplate()
    {
        return (new SiswaExport())->downloadTemplate('template-import-siswa.xlsx');
    }

    // ── Import langsung dari file unduhan Dapodik ──────────────────────────────
    public function importDapodik(Request $request)
    {
        $request->validate(['file_dapodik' => 'required|mimes:xlsx,xls|max:10240']);

        $filePath = $request->file('file_dapodik')->getRealPath();
        $import   = new DapodikImport();
        $import->import($filePath);

        return $this->handleImportResult($import->getErrors(), $import->getWarnings(), $import->getImportedCount(), $import->getSkippedCount(), ' dari Dapodik');
    }

    /**
     * Simpan hasil import. Baris error TIDAK lagi ditaruh penuh di session
     * (bisa sangat panjang) - disimpan sementara di cache dengan token, lalu
     * ditampilkan di halaman terpisah dengan navigasi halaman (maks 20/hal).
     */
    private function handleImportResult(array $errors, array $warnings, int $imported, int $skipped, string $suffix)
    {
        $errorToken = null;
        if (count($errors) > 0) {
            $errorToken = uniqid('imperr_', true);
            Cache::put("import_errors_{$errorToken}", $errors, now()->addHour());
        }

        if ($imported === 0 && count($errors) > 0) {
            return redirect()->route('siswa.import.form')
                ->with('error', "Import{$suffix} gagal. Tidak ada data yang berhasil disimpan.")
                ->with('import_error_token', $errorToken)
                ->with('import_error_count', count($errors));
        }

        $msg = "Berhasil mengimport {$imported} siswa{$suffix}.";
        if ($skipped > 0)        $msg .= " {$skipped} baris dilewati.";
        if (count($errors) > 0)  $msg .= " " . count($errors) . " baris error.";

        $type = (count($errors) > 0 || count($warnings) > 0) ? 'warning' : 'success';

        return redirect()->route('siswa.import.form')
            ->with($type, $msg)
            ->with('import_warnings',    $warnings)
            ->with('import_imported',    $imported)
            ->with('import_error_token', $errorToken)
            ->with('import_error_count', count($errors));
    }

    /**
     * Halaman terpisah menampilkan baris error import, dipaginasi 20/halaman,
     * diambil dari cache berdasarkan token (bukan disimpan permanen di DB).
     */
    public function importErrors(string $token, Request $request)
    {
        $errors = Cache::get("import_errors_{$token}");

        abort_if($errors === null, 404, 'Daftar error sudah kedaluwarsa (berlaku 1 jam) atau tidak ditemukan.');

        $perPage = 20;
        $page = Paginator::resolveCurrentPage('page');
        $items = array_slice($errors, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $items,
            count($errors),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('siswa.import-errors', ['errors' => $paginator, 'total' => count($errors)]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function validateSiswa(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nisn'             => 'required|string|max:20|unique:siswas,nisn'.($ignoreId ? ",{$ignoreId}" : ''),
            'nis'              => 'nullable|string|max:20',
            'nama_lengkap'     => 'required|string|max:100',
            'jenis_kelamin'    => 'required|in:L,P',
            'tempat_lahir'     => 'required|string|max:60',
            'tanggal_lahir'    => 'required|date',
            'agama'            => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'alamat'           => 'required|string',
            'rt'               => 'nullable|string|max:5',
            'rw'               => 'nullable|string|max:5',
            'dusun'            => 'nullable|string|max:60',
            'kelurahan'        => 'nullable|string|max:60',
            'kecamatan'        => 'nullable|string|max:60',
            'kode_pos'         => 'nullable|string|max:10',
            'lintang'          => 'nullable|numeric',
            'bujur'            => 'nullable|numeric',
            'no_telepon'       => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'nik'              => 'nullable|string|max:20',
            'no_kk'            => 'nullable|string|max:20',
            'kelas'            => 'required|string|max:10',
            'rombel'           => 'nullable|string|max:20',
            'tahun_masuk'      => 'required|digits:4|integer',
            'status'           => 'required|in:aktif,lulus,keluar,pindah',
            'tanggal_diterima' => 'nullable|date',
            'no_sttb_sd'       => 'nullable|string|max:50',
            'asal_sekolah'     => 'nullable|string|max:100',
            'no_un_sd'         => 'nullable|string|max:50',
            'anak_ke'          => 'nullable|integer',
            'golongan_darah'   => 'nullable|in:A,B,AB,O,Tidak Tahu',
            'tinggi_badan'     => 'nullable|integer',
            'berat_badan'      => 'nullable|integer',
            'riwayat_penyakit' => 'nullable|string|max:200',
            'nama_ayah'        => 'nullable|string|max:100',
            'tahun_lahir_ayah' => 'nullable|integer',
            'pendidikan_ayah'  => 'nullable|string|max:50',
            'pekerjaan_ayah'   => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|string|max:50',
            'nama_ibu'         => 'nullable|string|max:100',
            'tahun_lahir_ibu'  => 'nullable|integer',
            'pendidikan_ibu'   => 'nullable|string|max:50',
            'pekerjaan_ibu'    => 'nullable|string|max:100',
            'penghasilan_ibu'  => 'nullable|string|max:50',
            'nama_wali'        => 'nullable|string|max:100',
            'pekerjaan_wali'   => 'nullable|string|max:100',
            'no_telepon_ortu'  => 'nullable|string|max:20',
            'alamat_ortu'      => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }
}
