<?php

namespace App\Http\Controllers;

use App\Models\SarprasAsset;
use App\Models\SarprasRiwayatKerusakan;
use Illuminate\Http\Request;

class SarprasRiwayatKerusakanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status']);

        $riwayat = SarprasRiwayatKerusakan::with('asset')
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('tanggal_lapor')
            ->paginate(15)
            ->withQueryString();

        $assets = SarprasAsset::orderBy('nama_barang')->get();

        return view('sarpras.riwayat-kerusakan.index', compact('riwayat', 'assets', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:sarpras_assets,id',
            'tanggal_lapor' => 'required|date',
            'deskripsi_kerusakan' => 'required|string|max:1000',
        ]);

        $data['sekolah_id'] = auth()->user()->sekolah_id;
        $data['status'] = 'dilaporkan';

        SarprasRiwayatKerusakan::create($data);

        SarprasAsset::find($request->asset_id)?->update(['status' => 'dalam_perbaikan']);

        return back()->with('success', 'Kerusakan berhasil dilaporkan.');
    }

    public function update(Request $request, SarprasRiwayatKerusakan $riwayat)
    {
        $data = $request->validate([
            'status' => 'required|in:dilaporkan,diperbaiki,tidak_bisa_diperbaiki',
            'tanggal_selesai' => 'nullable|date',
            'biaya_perbaikan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $riwayat->update($data);

        if ($data['status'] === 'diperbaiki') {
            $riwayat->asset?->update(['status' => 'baik']);
        }

        return back()->with('success', 'Status kerusakan berhasil diperbarui.');
    }

    public function destroy(SarprasRiwayatKerusakan $riwayat)
    {
        $riwayat->delete();

        return back()->with('success', 'Catatan kerusakan berhasil dihapus.');
    }
}
