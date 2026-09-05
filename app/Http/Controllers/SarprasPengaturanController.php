<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SarprasPengaturanController extends Controller
{
    public function index()
    {
        return view('sarpras.pengaturan.index', ['sekolah' => auth()->user()->sekolah]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sarpras_prefix_kode' => 'nullable|string|max:20',
            'sarpras_ambang_batas_pinjam_hari' => 'required|integer|min:1|max:365',
        ]);

        auth()->user()->sekolah->update($data);

        return back()->with('success', 'Pengaturan Sarpras berhasil disimpan.');
    }
}
