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

        $sekolahs = Sekolah::withCount(['users', 'siswas' => fn ($q) => $q->withoutGlobalScopes()])
            ->when($search, fn ($q) => $q->where('nama', 'ilike', "%{$search}%")->orWhere('npsn', 'ilike', "%{$search}%"))
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
        $sekolah->loadCount(['users', 'siswas' => fn ($q) => $q->withoutGlobalScopes()]);
        $sekolah->load(['users' => fn ($q) => $q->orderBy('role')]);

        return Inertia::render('SuperAdmin/SekolahDetail', [
            'sekolah' => $sekolah,
        ]);
    }

    public function logAktivitas(Request $request): Response
    {
        $eventFilter = $request->input('event');
        $search = $request->input('search');

        $logs = \App\Models\ActivityLog::with(['user', 'sekolah'])
            ->when($eventFilter, fn ($q, $v) => $q->where('event', $v))
            ->when($search, fn ($q, $v) => $q->where(fn ($qq) => $qq
                ->where('nama_snapshot', 'ilike', "%{$v}%")
                ->orWhere('email_snapshot', 'ilike', "%{$v}%")))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('SuperAdmin/LogAktivitas', [
            'logs' => $logs,
            'filters' => ['event' => $eventFilter, 'search' => $search],
            'stats' => [
                'total_login' => \App\Models\ActivityLog::where('event', 'login')->count(),
                'total_logout' => \App\Models\ActivityLog::where('event', 'logout')->count(),
                'total_registrasi' => \App\Models\ActivityLog::where('event', 'registrasi')->count(),
            ],
        ]);
    }
}
