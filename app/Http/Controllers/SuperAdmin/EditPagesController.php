<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProgramPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EditPagesController extends Controller
{
    public function index()
    {
        $pages = ProgramPage::orderBy('title')->get();

        return Inertia::render('SuperAdmin/EditPagesIndex', ['pages' => $pages]);
    }

    public function edit(ProgramPage $page)
    {
        return Inertia::render('SuperAdmin/EditPagesForm', ['page' => $page]);
    }

    public function update(Request $request, ProgramPage $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'status' => 'required|string|max:30',
            'summary' => 'required|string|max:255',
            'detail' => 'required|string|max:2000',
            'href' => 'nullable|string|max:150',
            'cta' => 'nullable|string|max:100',
            'demo_href' => 'nullable|string|max:150',
        ]);

        $page->update($data);

        return redirect()->route('superadmin.edit-pages.index')->with('success', "Halaman \"{$page->title}\" berhasil disimpan.");
    }
}
