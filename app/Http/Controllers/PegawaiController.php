<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Exports\PegawaiExport;
use App\Imports\PegawaiImport;
use App\Imports\PegawaiDapodikImport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PegawaiController extends Controller
{
    private const JENIS_KEPEGAWAIAN = ['PNS', 'PPPK', 'GTT', 'PTT', 'GTY', 'PTY', 'Lainnya'];

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status_aktif', 'jenis_kepegawaian', 'unit_kerja', 'kategori_pegawai']);

        $pegawais = Pegawai::filter($filters)
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $unitKerjaList = Pegawai::query()
            ->whereNotNull('unit_kerja')
            ->distinct()
            ->orderBy('unit_kerja')
            ->pluck('unit_kerja');

        return view('pegawai.index', [
            'pegawais' => $pegawais,
            'filters' => $filters,
            'unitKerjaList' => $unitKerjaList,
            'totalSemua' => Pegawai::count(),
        ]);
    }

    public function create()
    {
        return view('pegawai.create', ['jenisList' => self::JENIS_KEPEGAWAIAN]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Pegawai::create($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.edit', ['pegawai' => $pegawai, 'jenisList' => self::JENIS_KEPEGAWAIAN]);
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['riwayatPendidikan', 'keluarga', 'cuti', 'mutasi']);
        return view('pegawai.show', ['pegawai' => $pegawai]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $data = $this->validated($request, $pegawai->id);
        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return back()->with('success', 'Data pegawai berhasil dihapus.');
    }

    // ── Export ──────────────────────────────────────────────────────────
    public function exportChoice(Request $request)
    {
        return view('pegawai.export', ['query' => $request->only(['search', 'status_aktif', 'jenis_kepegawaian', 'unit_kerja', 'kategori_pegawai'])]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'status_aktif', 'jenis_kepegawaian', 'unit_kerja', 'kategori_pegawai']);
        return (new PegawaiExport($filters))->download('data-pegawai-' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdfAll(Request $request)
    {
        ini_set('memory_limit', '512M');
        $filters = $request->only(['search', 'status_aktif', 'jenis_kepegawaian', 'unit_kerja', 'kategori_pegawai']);
        $pegawais = Pegawai::filter($filters)->orderBy('nama_lengkap')->get();
        return Pdf::loadView('pegawai.pdf-list', compact('pegawais'))
            ->setPaper('a4', 'landscape')
            ->download('data-pegawai-' . now()->format('Ymd') . '.pdf');
    }

    public function downloadTemplate()
    {
        return (new PegawaiExport())->downloadTemplate('template-import-pegawai.xlsx');
    }

    // ── Import ──────────────────────────────────────────────────────────
    public function showImport()
    {
        return view('pegawai.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $importer = new PegawaiImport();
        $importer->import($request->file('file')->getRealPath());

        return $this->handleImportResult(
            $importer->getErrors(),
            $importer->getWarnings(),
            $importer->getImportedCount(),
            $importer->getSkippedCount()
        );
    }

    public function importDapodik(Request $request)
    {
        $request->validate(['file_dapodik' => 'required|mimes:xlsx,xls|max:10240']);

        $importer = new PegawaiDapodikImport();
        $importer->import($request->file('file_dapodik')->getRealPath());

        return $this->handleImportResult(
            $importer->getErrors(),
            $importer->getWarnings(),
            $importer->getImportedCount(),
            $importer->getSkippedCount()
        );
    }

    private function handleImportResult(array $errors, array $warnings, int $imported, int $skipped)
    {
        $errorToken = null;
        if (count($errors) > 0) {
            $errorToken = uniqid('pegimperr_', true);
            Cache::put("import_errors_{$errorToken}", $errors, now()->addHour());
        }

        if ($imported === 0 && count($errors) > 0) {
            return redirect()->route('pegawai.import.form')
                ->with('error', 'Import gagal. Tidak ada data yang berhasil disimpan.')
                ->with('import_error_token', $errorToken)
                ->with('import_error_count', count($errors));
        }

        $msg = "Berhasil mengimport {$imported} pegawai.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";
        if (count($errors) > 0) $msg .= " " . count($errors) . " baris error.";

        $type = (count($errors) > 0 || count($warnings) > 0) ? 'warning' : 'success';

        return redirect()->route('pegawai.import.form')
            ->with($type, $msg)
            ->with('import_warnings', $warnings)
            ->with('import_error_token', $errorToken)
            ->with('import_error_count', count($errors));
    }

    public function importErrors(string $token, Request $request)
    {
        $errors = Cache::get("import_errors_{$token}");
        abort_if($errors === null, 404, 'Daftar error sudah kedaluwarsa (berlaku 1 jam) atau tidak ditemukan.');

        $perPage = 20;
        $page = Paginator::resolveCurrentPage('page');
        $items = array_slice($errors, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $items, count($errors), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pegawai.import-errors', ['errors' => $paginator, 'total' => count($errors)]);
    }

    // ── Laporan (read-only, dihitung otomatis dari data pegawai) ───────────

    /** Daftar Urut Kepangkatan - urut berdasarkan golongan lalu TMT pangkat */
    public function duk()
    {
        $pegawais = Pegawai::where('status_aktif', 'Aktif')
            ->whereIn('jenis_kepegawaian', Pegawai::STATUS_ASN)
            ->orderByDesc('golongan')
            ->orderBy('tmt_pangkat_terakhir')
            ->get();

        return view('pegawai.duk', ['pegawais' => $pegawais]);
    }

    /** Kendali Pangkat - siapa yang jatuh tempo kenaikan pangkat reguler (+4 tahun) */
    public function kendaliPangkat()
    {
        $pegawais = Pegawai::where('status_aktif', 'Aktif')
            ->whereIn('jenis_kepegawaian', Pegawai::STATUS_ASN)
            ->whereNotNull('tmt_pangkat_terakhir')
            ->get()
            ->sortBy(fn ($p) => $p->jatuh_tempo_pangkat);

        return view('pegawai.kendali-pangkat', ['pegawais' => $pegawais]);
    }

    /** Gaji Berkala - siapa yang jatuh tempo KGB (+2 tahun) */
    public function gajiBerkala()
    {
        $pegawais = Pegawai::where('status_aktif', 'Aktif')
            ->whereIn('jenis_kepegawaian', Pegawai::STATUS_ASN)
            ->whereNotNull('tmt_gaji_berkala_terakhir')
            ->get()
            ->sortBy(fn ($p) => $p->jatuh_tempo_gaji_berkala);

        return view('pegawai.gaji-berkala', ['pegawais' => $pegawais]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nip_nuptk' => 'nullable|string|max:30',
            'nik' => 'nullable|string|max:20',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kepegawaian' => 'required|in:' . implode(',', self::JENIS_KEPEGAWAIAN),
            'jabatan' => 'nullable|string|max:150',
            'kategori_pegawai' => 'nullable|in:Guru,Tenaga Kependidikan,Kepala Sekolah',
            'unit_kerja' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:20',
            'pangkat' => 'nullable|string|max:100',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'no_sk_pangkat' => 'nullable|string|max:100',
            'tmt_pangkat_terakhir' => 'nullable|date',
            'tmt_gaji_berkala_terakhir' => 'nullable|date',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'alamat' => 'nullable|string|max:255',
            'status_aktif' => 'required|in:Aktif,Cuti,Nonaktif,Pensiun,Pindah',
            'tanggal_masuk' => 'nullable|date',
        ]);
    }
}
