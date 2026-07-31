<?php

namespace App\Http\Controllers;

use App\Models\{CutiPegawai, KeluargaPegawai, MutasiPegawai, Pegawai, RiwayatPendidikanPegawai};
use Illuminate\Http\Request;

class PegawaiRiwayatController extends Controller
{
    // ── Riwayat Pendidikan ──────────────────────────────────────────────
    public function storePendidikan(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'jenjang' => 'required|string|max:20',
            'nama_institusi' => 'required|string|max:150',
            'jurusan' => 'nullable|string|max:100',
            'tahun_lulus' => 'nullable|digits:4',
            'no_ijazah' => 'nullable|string|max:100',
        ]);
        $pegawai->riwayatPendidikan()->create($data);
        return back()->with('success', 'Riwayat pendidikan ditambahkan.');
    }

    public function destroyPendidikan(Pegawai $pegawai, RiwayatPendidikanPegawai $riwayat)
    {
        $riwayat->delete();
        return back()->with('success', 'Riwayat pendidikan dihapus.');
    }

    // ── Tunjangan Keluarga ──────────────────────────────────────────────
    public function storeKeluarga(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'hubungan' => 'required|in:Suami,Istri,Anak',
            'tanggal_lahir' => 'nullable|date',
            'pekerjaan' => 'nullable|string|max:100',
            'masih_ditanggung' => 'nullable|boolean',
        ]);
        $data['masih_ditanggung'] = $request->boolean('masih_ditanggung', true);
        $pegawai->keluarga()->create($data);
        return back()->with('success', 'Data keluarga ditambahkan.');
    }

    public function destroyKeluarga(Pegawai $pegawai, KeluargaPegawai $keluarga)
    {
        $keluarga->delete();
        return back()->with('success', 'Data keluarga dihapus.');
    }

    // ── Rekap Cuti ──────────────────────────────────────────────────────
    public function storeCuti(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'jenis_cuti' => 'required|in:Tahunan,Sakit,Melahirkan,Besar,Alasan Penting,Diluar Tanggungan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'no_surat' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);
        $data['jumlah_hari'] = \Carbon\Carbon::parse($data['tanggal_mulai'])
            ->diffInDays(\Carbon\Carbon::parse($data['tanggal_selesai'])) + 1;
        $pegawai->cuti()->create($data);
        return back()->with('success', 'Data cuti ditambahkan.');
    }

    public function destroyCuti(Pegawai $pegawai, CutiPegawai $cuti)
    {
        $cuti->delete();
        return back()->with('success', 'Data cuti dihapus.');
    }

    // ── Rekap Mutasi ────────────────────────────────────────────────────
    public function storeMutasi(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'jenis_mutasi' => 'required|in:Masuk,Keluar,Internal',
            'tanggal_mutasi' => 'required|date',
            'asal' => 'nullable|string|max:150',
            'tujuan' => 'nullable|string|max:150',
            'no_sk' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);
        $pegawai->mutasi()->create($data);
        return back()->with('success', 'Data mutasi ditambahkan.');
    }

    public function destroyMutasi(Pegawai $pegawai, MutasiPegawai $mutasi)
    {
        $mutasi->delete();
        return back()->with('success', 'Data mutasi dihapus.');
    }
}
