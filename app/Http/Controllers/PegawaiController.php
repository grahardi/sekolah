<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    private const JENIS_KEPEGAWAIAN = ['PNS', 'PPPK', 'GTT', 'PTT', 'GTY', 'PTY', 'Lainnya'];

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status_aktif', 'jenis_kepegawaian', 'unit_kerja']);

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

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nip_nuptk' => 'nullable|string|max:30',
            'nama_lengkap' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kepegawaian' => 'required|in:' . implode(',', self::JENIS_KEPEGAWAIAN),
            'jabatan' => 'nullable|string|max:150',
            'unit_kerja' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:20',
            'pangkat' => 'nullable|string|max:100',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'no_sk_pangkat' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'alamat' => 'nullable|string|max:255',
            'status_aktif' => 'required|in:Aktif,Cuti,Nonaktif,Pensiun,Pindah',
            'tanggal_masuk' => 'nullable|date',
        ]);
    }
}
