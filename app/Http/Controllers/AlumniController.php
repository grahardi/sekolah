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
        $request->validate(['file_dapodik' => 'required|mimes:xlsx,xls|max:10240']);

        $filePath = $request->file('file_dapodik')->getRealPath();
        $import = new DapodikImport('lulus');
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
}
