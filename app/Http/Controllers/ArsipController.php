<?php

namespace App\Http\Controllers;

use App\Models\{Siswa, ArsipBerkas};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function show(Siswa $siswa)
    {
        $arsip = $siswa->arsipBerkas ?? new ArsipBerkas(['siswa_id' => $siswa->id]);
        return view('siswa.arsip', compact('siswa','arsip'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'foto'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'akta_lahir'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah_sd'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transkrip_nilai'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sertifikat_tka' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan'        => 'nullable|string|max:500',
        ]);

        $arsip = $siswa->arsipBerkas ?? new ArsipBerkas(['siswa_id' => $siswa->id]);
        $fields = ['foto','kartu_keluarga','akta_lahir','ijazah_sd','ijazah','transkrip_nilai','sertifikat_tka'];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                if ($arsip->$field) Storage::disk('public')->delete($arsip->$field);
                $arsip->$field = $request->file($field)->store("arsip/{$siswa->id}", 'public');
            }
        }

        $arsip->catatan  = $request->catatan;
        $arsip->siswa_id = $siswa->id;
        $arsip->save();

        return back()->with('success','Arsip berkas berhasil disimpan.');
    }

    public function hapusBerkas(Request $request, Siswa $siswa)
    {
        $request->validate([
            'field' => 'required|in:foto,kartu_keluarga,akta_lahir,ijazah_sd,ijazah,transkrip_nilai,sertifikat_tka'
        ]);

        $arsip = $siswa->arsipBerkas;
        if ($arsip && $arsip->{$request->field}) {
            Storage::disk('public')->delete($arsip->{$request->field});
            $arsip->update([$request->field => null]);
        }

        return back()->with('success','Berkas berhasil dihapus.');
    }
}
