<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ModulAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ModulAjarController extends Controller
{
    public function index(Request $request)
    {
        $mapel = $request->input('mapel');

        $modules = ModulAjar::when($mapel, fn ($q, $v) => $q->where('mapel', $v))
            ->orderBy('mapel')->orderBy('urutan')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'mapel' => $m->mapel,
                'kelas' => $m->kelas,
                'fase' => $m->fase,
                'title' => $m->title,
                'deskripsi' => $m->deskripsi,
                'file_key' => $m->file_key,
                'aktif' => $m->aktif,
                'sumber' => $m->sumber,
                'exists_locally' => $m->exists_locally,
            ]);

        $mapelList = ModulAjar::select('mapel')->distinct()->orderBy('mapel')->pluck('mapel');

        return Inertia::render('SuperAdmin/ModulAjarIndex', [
            'modules' => $modules,
            'mapelList' => $mapelList,
            'filterMapel' => $mapel,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mapel' => 'required|string|max:100',
            'kelas' => 'required|integer|min:7|max:9',
            'fase' => 'required|string|max:5',
            'title' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'file_key' => 'required|string|max:100|unique:modul_ajar,file_key',
            'drive_id' => 'nullable|string|max:100',
            'file_docx' => 'nullable|file|mimes:doc,docx|max:20480',
        ]);

        if ($request->hasFile('file_docx')) {
            $request->file('file_docx')->storeAs('modul-ajar', $data['file_key'] . '.docx', 'public');
        }
        unset($data['file_docx']);

        ModulAjar::create($data);

        return back()->with('success', 'Modul ajar berhasil ditambahkan.');
    }

    public function update(Request $request, ModulAjar $modul)
    {
        $data = $request->validate([
            'mapel' => 'required|string|max:100',
            'kelas' => 'required|integer|min:7|max:9',
            'fase' => 'required|string|max:5',
            'title' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'drive_id' => 'nullable|string|max:100',
            'aktif' => 'nullable|boolean',
            'file_docx' => 'nullable|file|mimes:doc,docx|max:20480',
        ]);

        if ($request->hasFile('file_docx')) {
            $request->file('file_docx')->storeAs('modul-ajar', $modul->file_key . '.docx', 'public');
        }
        unset($data['file_docx']);

        $modul->update($data);

        return back()->with('success', 'Modul ajar berhasil diperbarui.');
    }

    public function destroy(ModulAjar $modul)
    {
        if ($modul->exists_locally) {
            Storage::disk('public')->delete($modul->local_path);
        }
        $modul->delete();

        return back()->with('success', 'Modul ajar berhasil dihapus.');
    }
}
