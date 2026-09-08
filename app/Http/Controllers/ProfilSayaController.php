<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSayaController extends Controller
{
    public function edit()
    {
        $guru = Guru::where('user_id', auth()->id())->first();

        if ($guru && $guru->pegawai_id) {
            $pegawai = $guru->pegawai;
            $data = [
                'nama_lengkap' => $pegawai->nama_lengkap,
                'nip_nuptk' => $pegawai->nip_nuptk,
                'nik' => $pegawai->nik,
                'tempat_lahir' => $pegawai->tempat_lahir,
                'tanggal_lahir' => $pegawai->tanggal_lahir?->format('Y-m-d'),
                'no_hp' => $pegawai->no_hp,
                'email' => $pegawai->email,
                'alamat' => $pegawai->alamat,
                'foto_url' => $pegawai->foto ? asset('storage/' . $pegawai->foto) : null,
            ];
            $sumber = 'pegawai';
        } elseif ($guru) {
            $data = [
                'nama_lengkap' => $guru->nama,
                'nip_nuptk' => $guru->nip_nuptk,
                'nik' => null, 'tempat_lahir' => null, 'tanggal_lahir' => null,
                'no_hp' => null, 'email' => null, 'alamat' => null,
                'foto_url' => $guru->foto_url,
            ];
            $sumber = 'guru';
        } else {
            $data = [
                'nama_lengkap' => auth()->user()->name, 'nip_nuptk' => null, 'nik' => null,
                'tempat_lahir' => null, 'tanggal_lahir' => null, 'no_hp' => null,
                'email' => auth()->user()->email, 'alamat' => null, 'foto_url' => null,
            ];
            $sumber = 'user_saja';
        }

        return view('profil-saya.edit', ['data' => $data, 'sumber' => $sumber]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nip_nuptk' => 'nullable|string|max:30',
            'nik' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:60',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $guru = Guru::where('user_id', auth()->id())->first();

        if ($guru && $guru->pegawai_id) {
            $pegawai = $guru->pegawai;
            if ($request->hasFile('foto')) {
                if ($pegawai->foto) Storage::disk('public')->delete($pegawai->foto);
                $validated['foto'] = $request->file('foto')->store('pegawai/foto', 'public');
            }
            $pegawai->update($validated);

            $guru->update(['nama' => $validated['nama_lengkap'], 'nip_nuptk' => $validated['nip_nuptk']]);
            auth()->user()->update(['name' => $validated['nama_lengkap']]);
        } elseif ($guru) {
            $guru->update(['nama' => $validated['nama_lengkap'], 'nip_nuptk' => $validated['nip_nuptk']]);
            auth()->user()->update(['name' => $validated['nama_lengkap']]);
        } else {
            auth()->user()->update(['name' => $validated['nama_lengkap']]);
        }

        return back()->with('success', 'Data profil berhasil diperbarui.');
    }
}
