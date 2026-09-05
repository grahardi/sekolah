<?php

namespace App\Http\Controllers;

use App\Models\SarprasAsset;
use App\Models\SarprasCategory;
use App\Models\SarprasFundingSource;
use App\Models\SarprasLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SarprasAssetController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'location_id', 'status']);

        $assets = SarprasAsset::with(['category', 'location', 'fundingSource'])
            ->filter($filters)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = SarprasCategory::orderBy('name')->get();
        $locations = SarprasLocation::orderBy('name')->get();

        return view('sarpras.assets.index', compact('assets', 'categories', 'locations', 'filters'));
    }

    public function create()
    {
        $categories = SarprasCategory::orderBy('name')->get();
        $locations = SarprasLocation::orderBy('name')->get();
        $fundingSources = SarprasFundingSource::orderBy('name')->get();
        $nextKode = SarprasAsset::generateNextKodeBarang(auth()->user()->sekolah_id);

        return view('sarpras.assets.create', compact('categories', 'locations', 'fundingSources', 'nextKode'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('sarpras', 'public');
        }

        SarprasAsset::create($data);

        return redirect()->route('sarpras.assets.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(SarprasAsset $asset)
    {
        $categories = SarprasCategory::orderBy('name')->get();
        $locations = SarprasLocation::orderBy('name')->get();
        $fundingSources = SarprasFundingSource::orderBy('name')->get();

        return view('sarpras.assets.edit', compact('asset', 'categories', 'locations', 'fundingSources'));
    }

    public function update(Request $request, SarprasAsset $asset)
    {
        $data = $this->validateData($request, $asset);

        if ($request->boolean('hapus_foto') && $asset->foto) {
            Storage::disk('public')->delete($asset->foto);
            $data['foto'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($asset->foto) {
                Storage::disk('public')->delete($asset->foto);
            }
            $data['foto'] = $request->file('foto')->store('sarpras', 'public');
        }

        $asset->update($data);

        return redirect()->route('sarpras.assets.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(SarprasAsset $asset)
    {
        if ($asset->foto) {
            Storage::disk('public')->delete($asset->foto);
        }

        $asset->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    public function show(SarprasAsset $asset)
    {
        $asset->load(['category', 'location', 'fundingSource', 'riwayatKerusakan', 'peminjaman']);
        return view('sarpras.assets.show', compact('asset'));
    }

    private function validateData(Request $request, ?SarprasAsset $asset = null): array
    {
        $assetId = $asset?->id;
        $sekolahId = auth()->user()->sekolah_id;

        $data = $request->validate([
            'kode_barang' => [
                'required', 'string', 'max:50',
                Rule::unique('sarpras_assets', 'kode_barang')->where(fn ($q) => $q->where('sekolah_id', $sekolahId))->ignore($assetId),
            ],
            'kode_umum' => 'nullable|string|max:50',
            'kode_aset' => 'nullable|string|max:50',
            'nama_barang' => 'required|string|max:200',
            'category_id' => 'nullable|exists:sarpras_categories,id',
            'location_id' => 'nullable|exists:sarpras_locations,id',
            'tahun_pembelian' => 'nullable|digits:4|integer|min:1990|max:' . (date('Y') + 1),
            'funding_source_id' => 'nullable|exists:sarpras_funding_sources,id',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:baik,rusak,dalam_perbaikan',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'kode_barang.unique' => 'Kode Barang sudah digunakan barang lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        unset($data['foto']); // path finalnya diisi terpisah di store()/update()

        return $data;
    }
}
