<?php

namespace App\Http\Controllers;

use App\Models\ModulAjar;
use Inertia\Inertia;
use Inertia\Response;

class ModulAjarController extends Controller
{
    public function index(): Response
    {
        $modules = ModulAjar::where('aktif', true)
            ->orderBy('mapel')->orderBy('urutan')
            ->get()
            ->map(fn ($m) => [
                'mapel' => $m->mapel,
                'kelas' => $m->kelas,
                'fase' => $m->fase,
                'title' => $m->title,
                'desc' => $m->deskripsi,
                'tipe' => 'docx',
                'download_url' => $m->download_url,
                'sumber' => $m->sumber,
            ]);

        return Inertia::render('ModulAjar/Index', [
            'modules' => $modules,
            'mapelList' => $modules->pluck('mapel')->unique()->values(),
        ]);
    }
}
