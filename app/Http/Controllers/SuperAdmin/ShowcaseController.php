<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ShowcaseController extends Controller
{
    public function index()
    {
        $items = ShowcaseItem::orderBy('urutan')->get();

        return Inertia::render('SuperAdmin/ShowcaseIndex', ['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'subjudul' => 'nullable|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'link' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|max:4096',
            'urutan' => 'nullable|integer',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('showcase', 'public');
        }

        ShowcaseItem::create($data);

        return back()->with('success', 'Showcase berhasil ditambahkan.');
    }

    public function update(Request $request, ShowcaseItem $showcase)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'subjudul' => 'nullable|string|max:150',
            'deskripsi' => 'nullable|string|max:1000',
            'link' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|max:4096',
            'urutan' => 'nullable|integer',
            'aktif' => 'nullable|boolean',
        ]);

        if ($request->hasFile('gambar')) {
            if ($showcase->gambar) {
                Storage::disk('public')->delete($showcase->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('showcase', 'public');
        }

        $showcase->update($data);

        return back()->with('success', 'Showcase berhasil diperbarui.');
    }

    public function destroy(ShowcaseItem $showcase)
    {
        if ($showcase->gambar) {
            Storage::disk('public')->delete($showcase->gambar);
        }
        $showcase->delete();

        return back()->with('success', 'Showcase berhasil dihapus.');
    }
}
