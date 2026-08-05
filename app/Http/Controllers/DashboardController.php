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

        $rekapKelas = Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->select('kelas', 'rombel',
                \Illuminate\Support\Facades\DB::raw('count(*) as total'),
                \Illuminate\Support\Facades\DB::raw("sum(case when jenis_kelamin='L' then 1 else 0 end) as laki"),
                \Illuminate\Support\Facades\DB::raw("sum(case when jenis_kelamin='P' then 1 else 0 end) as perempuan"))
            ->groupBy('kelas', 'rombel')->orderBy('kelas')->orderBy('rombel')->get();

        $totalGuru = \App\Models\Guru::count();

        $totalSurveyPeserta = \App\Models\SurveyPeserta::count();
        $surveySudahIsi = \App\Models\SurveyJawaban::select('peserta_id')->distinct()->count();

        return Inertia::render('Dashboard', [
            'sekolah' => $request->user()->sekolah,
            'stats' => [
                // Siswa/Pegawai/Survey model punya global scope sekolah_id
                // otomatis, jadi count() ini sudah pasti cuma sekolah sendiri.
                'total_siswa' => Siswa::count(),
                'total_pegawai' => Pegawai::count(),
                'total_survey' => Survey::count(),
                'total_user' => User::where('sekolah_id', $sekolahId)->count(),
                'total_guru' => $totalGuru,
            ],
            'rekapKelas' => $rekapKelas,
            'bkStats' => [
                'total_peserta' => $totalSurveyPeserta,
                'sudah_isi' => $surveySudahIsi,
            ],
        ]);
    }
}
