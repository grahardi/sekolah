<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SekolahProfilController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('SekolahProfil/Edit', [
            'sekolah' => $request->user()->sekolah,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $sekolah = $request->user()->sekolah;
        abort_unless($sekolah, 404);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'website' => 'nullable|string|max:150',
            'kepala_sekolah_nama' => 'nullable|string|max:150',
            'kepala_sekolah_nip' => 'nullable|string|max:30',
            'kepala_sekolah_pangkat' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'status_sekolah' => 'nullable|string|max:100',
            'bentuk_pendidikan' => 'nullable|string|max:100',
            'kkm' => 'nullable|integer|min:0|max:100',
        ]);

        $sekolah->update($validated);

        return redirect()->route('dashboard')->with('success', 'Profil sekolah berhasil diperbarui.');
    }
}
