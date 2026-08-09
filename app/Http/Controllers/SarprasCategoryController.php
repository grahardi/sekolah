<?php

namespace App\Http\Controllers;

use App\Models\SarprasCategory;
use Illuminate\Http\Request;

class SarprasCategoryController extends Controller
{
    public function index()
    {
        $tree = SarprasCategory::tree();
        $allCategories = SarprasCategory::orderBy('name')->get(); // utk dropdown pilih induk
        $iconOptions = SarprasCategory::iconOptions();

        return view('sarpras.categories.index', compact('tree', 'allCategories', 'iconOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:sarpras_categories,id',
            'icon' => 'nullable|string|max:100',
        ]);

        SarprasCategory::create($data); // sekolah_id auto-terisi via trait

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, SarprasCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:sarpras_categories,id',
            'icon' => 'nullable|string|max:100',
        ]);

        if (($data['parent_id'] ?? null) == $category->id) {
            return back()->withErrors(['parent_id' => 'Kategori tidak boleh menjadi induk dirinya sendiri.']);
        }

        $category->update($data);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(SarprasCategory $category)
    {
        if ($category->children()->exists()) {
            return back()->withErrors(['error' => 'Hapus dulu sub-kategori di dalamnya.']);
        }
        if ($category->assets()->exists()) {
            return back()->withErrors(['error' => 'Kategori masih dipakai oleh data barang.']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
