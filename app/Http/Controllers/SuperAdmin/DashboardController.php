<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $sekolahs = Sekolah::withCount(['users', 'siswas'])
            ->when($search, fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('npsn', 'like', "%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('SuperAdmin/Dashboard', [
            'sekolahs' => $sekolahs,
            'stats' => [
                'total_sekolah' => Sekolah::count(),
                'total_user'    => User::count(),
                'total_siswa'   => \App\Models\Siswa::withoutGlobalScopes()->count(),
            ],
            'filters' => ['search' => $search],
        ]);
    }

    public function show(Sekolah $sekolah): Response
    {
        $sekolah->loadCount(['users', 'siswas']);
        $sekolah->load(['users' => fn ($q) => $q->orderBy('role')]);

        return Inertia::render('SuperAdmin/SekolahDetail', [
            'sekolah' => $sekolah,
        ]);
    }
}
