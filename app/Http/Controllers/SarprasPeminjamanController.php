<?php

namespace App\Http\Controllers;

use App\Models\SarprasAsset;
use App\Models\SarprasPeminjaman;
use Illuminate\Http\Request;

class SarprasPeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status']);

        $peminjaman = SarprasPeminjaman::with('asset')
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('tanggal_pinjam')
            ->paginate(15)
            ->withQueryString();

        $assetIdDipinjam = SarprasPeminjaman::where('status', 'dipinjam')->pluck('asset_id');
        $assetsTersedia = SarprasAsset::whereNotIn('id', $assetIdDipinjam)->orderBy('nama_barang')->get();

        $sekolah = auth()->user()->sekolah;

        return view('sarpras.peminjaman.index', compact('peminjaman', 'assetsTersedia', 'filters', 'sekolah'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:sarpras_assets,id',
            'peminjam_nama' => 'required|string|max:150',
            'peminjam_kontak' => 'nullable|string|max:50',
            'keperluan' => 'nullable|string|max:255',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $sudahDipinjam = SarprasPeminjaman::where('asset_id', $data['asset_id'])->where('status', 'dipinjam')->exists();
        if ($sudahDipinjam) {
            return back()->with('error', 'Barang ini sedang dipinjam, belum bisa dipinjamkan lagi.');
        }

        $data['sekolah_id'] = auth()->user()->sekolah_id;
        $data['status'] = 'dipinjam';

        SarprasPeminjaman::create($data);

        return back()->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function kembalikan(Request $request, SarprasPeminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali_aktual' => now(),
        ]);

        return back()->with('success', 'Barang berhasil ditandai sudah dikembalikan.');
    }

    public function destroy(SarprasPeminjaman $peminjaman)
    {
        $peminjaman->delete();

        return back()->with('success', 'Catatan peminjaman berhasil dihapus.');
    }
}
