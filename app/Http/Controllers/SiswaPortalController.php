<?php

namespace App\Http\Controllers;

use App\Models\Rapor;
use App\Models\RaporDetailAkademik;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\RaporCalculator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class SiswaPortalController extends Controller
{
    public function showLogin()
    {
        if (session('siswa_portal_id')) {
            return redirect()->route('siswa-portal.dashboard');
        }
        return view('siswa-portal.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'nisn' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $siswa = Siswa::withoutGlobalScopes()
            ->where('nisn', trim($data['nisn']))
            ->whereDate('tanggal_lahir', $data['tanggal_lahir'])
            ->where('status', 'aktif')
            ->first();

        if (! $siswa) {
            return back()->withErrors(['nisn' => 'NISN atau tanggal lahir tidak cocok. Coba periksa lagi.'])->withInput();
        }

        session(['siswa_portal_id' => $siswa->id]);
        return redirect()->route('siswa-portal.dashboard');
    }

    public function logout()
    {
        session()->forget('siswa_portal_id');
        return redirect()->route('siswa-portal.login');
    }

    public function dashboard()
    {
        $siswa = $this->siswaSesiAtauGagal();
        return $this->tampilkanDashboard($siswa);
    }

    /** Akses langsung via QR code (link dari rapor cetak) - tanpa perlu login NISN+tanggal lahir */
    public function lihatViaQr(string $token)
    {
        $id = base64_decode(strtr($token, '-_', '+/'));
        $siswa = Siswa::withoutGlobalScopes()->where('id', $id)->where('status', 'aktif')->first();
        abort_unless($siswa, 404);

        return $this->tampilkanDashboard($siswa, viaQr: true);
    }

    private function tampilkanDashboard(Siswa $siswa, bool $viaQr = false)
    {
        $tahunAktif = TahunAjaran::withoutGlobalScopes()->where('sekolah_id', $siswa->sekolah_id)->where('is_aktif', true)->first();
        $semester = $tahunAktif?->semester === 'Genap' ? 2 : 1;

        $rapor = $tahunAktif
            ? Rapor::withoutGlobalScopes()->where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $tahunAktif->id)->where('semester', $semester)->with('detailAkademik.mataPelajaran')->first()
            : null;

        $mapelData = collect();
        if ($rapor) {
            foreach ($rapor->detailAkademik as $d) {
                $perTp = RaporCalculator::nilaiPerTp($siswa->id, $siswa->kelas, $siswa->rombel, $d->mata_pelajaran_id, $tahunAktif->id, $semester);
                $mapelData->push(['mapel' => $d->mataPelajaran, 'nilai_akhir' => $d->nilai_katrol ?? $d->nilai_akhir, 'per_tp' => $perTp]);
            }
        }

        return view('siswa-portal.dashboard', [
            'siswa' => $siswa,
            'tahunAktif' => $tahunAktif,
            'rapor' => $rapor,
            'mapelData' => $mapelData,
            'viaQr' => $viaQr,
        ]);
    }

    private function siswaSesiAtauGagal(): Siswa
    {
        $id = session('siswa_portal_id');
        abort_unless($id, 403, 'Silakan login dulu.');
        $siswa = Siswa::withoutGlobalScopes()->find($id);
        abort_unless($siswa, 403);
        return $siswa;
    }

    /** Cetak Laporan Hasil Belajar Tengah Semester (UTS/PTS) - dipanggil admin/wali kelas dari Cetak Rapor */
    public function cetakUts(Siswa $siswa)
    {
        ini_set('memory_limit', '512M');
        $sekolah = $siswa->sekolah;
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        abort_unless($tahunAktif, 422, 'Belum ada tahun ajaran aktif.');
        $semester = $tahunAktif->semester === 'Genap' ? 2 : 1;

        $mapelIds = \App\Models\GuruPengajar::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('kelas', $siswa->kelas)->where('rombel', $siswa->rombel)
            ->with('mataPelajaran')->get()->pluck('mataPelajaran')->unique('id')->sortBy('nama')->values();

        $rows = $mapelIds->map(function ($mapel) use ($siswa, $tahunAktif, $semester) {
            $perTp = RaporCalculator::nilaiPerTp($siswa->id, $siswa->kelas, $siswa->rombel, $mapel->id, $tahunAktif->id, $semester);
            $sts = RaporCalculator::nilaiSts($siswa->id, $siswa->kelas, $siswa->rombel, $mapel->id, $tahunAktif->id, $semester);
            return ['mapel' => $mapel, 'per_tp' => array_column($perTp, 'nilai'), 'sts' => $sts];
        });

        $token = rtrim(strtr(base64_encode((string) $siswa->id), '+/', '-_'), '=');
        $qrUrl = url("/siswa/qr/{$token}");

        $qrPng = null;
        if (class_exists(QrCode::class)) {
            $qrCode = new QrCode($qrUrl);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrPng = 'data:image/png;base64,' . base64_encode($result->getString());
        }

        $catatanWaliKelas = Rapor::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $tahunAktif->id)->where('semester', $semester)->value('catatan_wali_kelas');

        $pdf = Pdf::loadView('siswa-portal.cetak-uts', [
            'siswa' => $siswa,
            'sekolah' => $sekolah,
            'tahunAktif' => $tahunAktif,
            'semester' => $semester,
            'rows' => $rows,
            'qrPng' => $qrPng,
            'catatanWaliKelas' => $catatanWaliKelas,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('uts-' . str_replace(' ', '-', $siswa->nama_lengkap) . '.pdf');
    }
}
