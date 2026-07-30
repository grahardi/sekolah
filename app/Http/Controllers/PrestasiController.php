<?php

namespace App\Http\Controllers;

use App\Models\{Siswa, Prestasi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrestasiController extends Controller
{
    public function index(Siswa $siswa)
    {
        $prestasis = $siswa->prestasis()->paginate(15);
        return view('siswa.prestasi', compact('siswa','prestasis'));
    }

    public function store(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'tanggal_kegiatan' => 'required|date',
            'jenis_lomba'      => 'required|string|max:100',
            'tingkat_lomba'    => 'required|in:Sekolah,Kecamatan,Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'juara'            => 'required|string|max:30',
            'penyelenggara'    => 'nullable|string|max:100',
            'keterangan'       => 'nullable|string|max:500',
            'sertifikat'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store("prestasi/{$siswa->id}", 'public');
        }

        $siswa->prestasis()->create($data);
        return back()->with('success','Prestasi berhasil ditambahkan.');
    }

    public function destroy(Siswa $siswa, Prestasi $prestasi)
    {
        if ($prestasi->sertifikat) Storage::disk('public')->delete($prestasi->sertifikat);
        $prestasi->delete();
        return back()->with('success','Data prestasi dihapus.');
    }

    public function updateSertifikat(Request $request, Siswa $siswa, Prestasi $prestasi)
    {
        $request->validate(['sertifikat' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']);
        if ($prestasi->sertifikat) Storage::disk('public')->delete($prestasi->sertifikat);
        $prestasi->update([
            'sertifikat' => $request->file('sertifikat')->store("prestasi/{$siswa->id}", 'public'),
        ]);
        return back()->with('success','Sertifikat berhasil diupload.');
    }
}
