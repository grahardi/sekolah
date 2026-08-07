<?php

namespace App\Http\Controllers;

use App\Models\ExoInstance;
use App\Models\ExoRequest;
use Illuminate\Http\Request;

class ServerUjianController extends Controller
{
    public function index()
    {
        $sekolahId = auth()->user()->sekolah_id;
        $instance = ExoInstance::where('sekolah_id', $sekolahId)->first();

        $pid = null;
        if ($instance) {
            $pid = $instance->cekPid();
        }

        $requestAktif = ExoRequest::where('sekolah_id', $sekolahId)->where('status', 'Menunggu')->latest()->first();

        return view('server-ujian.index', [
            'instance' => $instance,
            'sedangJalan' => $pid !== null,
            'pid' => $pid,
            'requestAktif' => $requestAktif,
        ]);
    }

    public function ajukanRequest(Request $request)
    {
        $data = $request->validate(['catatan' => 'nullable|string|max:500']);

        ExoRequest::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'diminta_oleh_user_id' => auth()->id(),
            'catatan' => $data['catatan'] ?? null,
        ]);

        return back()->with('success', 'Permintaan Server Ujian berhasil dikirim. Tim kami akan segera memprosesnya.');
    }

    public function run(ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);

        if ($instance->cekPid() !== null) {
            return back()->with('error', 'Server sudah jalan.');
        }

        $namaBinary = collect(glob(rtrim($instance->path, '/') . '/main-amd64*'))->map(fn ($p) => basename($p))->first();
        abort_unless($namaBinary, 404, 'Binary tidak ditemukan.');

        \Illuminate\Support\Facades\Process::path($instance->path)->timeout(5)
            ->start("bash -c \"nohup ./{$namaBinary} > nohup.out 2>&1 & disown\"");
        sleep(1);

        $instance->update(['terakhir_dijalankan' => now()]);

        return back()->with('success', 'Server Ujian berhasil dijalankan.');
    }

    public function stop(ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);

        $pid = $instance->cekPid();
        if ($pid === null) {
            return back()->with('error', 'Server sedang tidak jalan.');
        }

        \Illuminate\Support\Facades\Process::run(['kill', (string) $pid]);

        return back()->with('success', 'Server Ujian dihentikan.');
    }

    public function sinkronSiswa(Request $request, ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);

        $identifier = $request->input('identifier', 'nisn') === 'nis' ? 'nis' : 'nisn';
        $hasil = \App\Services\ExoSyncService::sinkronSiswa($instance, $identifier);

        return back()->with($hasil['ok'] ? 'success' : 'error', $hasil['pesan']);
    }

    public function autoLogin(ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);
        abort_unless($instance->admin_email_tersambung && $instance->admin_password_tersambung, 422, 'Kredensial login belum di-generate. Hubungi admin sistem.');

        $port = $instance->bacaEnv('SERVER_PORT');
        $baseUrl = "http://163.227.0.18:{$port}";

        $token = null;
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->post("{$baseUrl}/api/v1/token/generate", [
                'email' => $instance->admin_email_tersambung,
                'password' => $instance->admin_password_tersambung,
            ]);
            $token = $response->json('data.token');
        } catch (\Throwable $e) {
            // biarkan $token null, halaman fallback tetap tampilkan cara manual
        }

        return view('server-ujian.auto-login', [
            'baseUrl' => $baseUrl,
            'token' => $token,
            // KEY OBFUSCATED dari build frontend exo SAAT INI - bisa berubah
            // kalau mereka update versi frontend-nya, karena namanya hasil
            // minifikasi/obfuscation, bukan nama tetap yg didokumentasikan.
            'localStorageKey' => '5m7VQI69HS2PrcToRMYt',
        ]);
    }
}
