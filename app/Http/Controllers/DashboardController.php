<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $sekolahId = $request->user()->sekolah_id;

        return Inertia::render('Dashboard', [
            'stats' => [
                // Siswa/Pegawai/Survey model punya global scope sekolah_id
                // otomatis, jadi count() ini sudah pasti cuma sekolah sendiri.
                'total_siswa' => Siswa::count(),
                'total_pegawai' => Pegawai::count(),
                'total_survey' => Survey::count(),
                'total_user' => User::where('sekolah_id', $sekolahId)->count(),
            ],
        ]);
    }
}
