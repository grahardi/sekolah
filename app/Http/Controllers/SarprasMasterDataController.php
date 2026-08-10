<?php

namespace App\Http\Controllers;

use App\Models\SarprasFundingSource;
use App\Models\SarprasLocation;
use Illuminate\Http\Request;

class SarprasMasterDataController extends Controller
{
    public function index()
    {
        $locations = SarprasLocation::withCount('assets')->orderBy('name')->get();
        $fundingSources = SarprasFundingSource::withCount('assets')->orderBy('name')->get();

        return view('sarpras.master-data.index', compact('locations', 'fundingSources'));
    }

    public function storeLocation(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        SarprasLocation::create($data);

        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function updateLocation(Request $request, SarprasLocation $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        $location->update($data);

        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroyLocation(SarprasLocation $location)
    {
        if ($location->assets()->exists()) {
            return back()->withErrors(['error' => 'Lokasi masih dipakai oleh data barang.']);
        }

        $location->delete();

        return back()->with('success', 'Lokasi berhasil dihapus.');
    }

    public function storeFundingSource(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        SarprasFundingSource::create($data);

        return back()->with('success', 'Sumber dana berhasil ditambahkan.');
    }

    public function updateFundingSource(Request $request, SarprasFundingSource $fundingSource)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'keterangan' => 'nullable|string',
        ]);

        $fundingSource->update($data);

        return back()->with('success', 'Sumber dana berhasil diperbarui.');
    }

    public function destroyFundingSource(SarprasFundingSource $fundingSource)
    {
        if ($fundingSource->assets()->exists()) {
            return back()->withErrors(['error' => 'Sumber dana masih dipakai oleh data barang.']);
        }

        $fundingSource->delete();

        return back()->with('success', 'Sumber dana berhasil dihapus.');
    }
}
