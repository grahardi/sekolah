<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ExoInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Inertia\Inertia;
use Inertia\Response;

class ExoInstanceController extends Controller
{
    public function index(): Response
    {
        $instances = ExoInstance::orderBy('nama')->get()->map(function ($i) {
            return [
                'id' => $i->id,
                'nama' => $i->nama,
                'slug' => $i->slug,
                'path' => $i->path,
                'is_aktif' => $i->is_aktif,
                'terakhir_dijalankan' => $i->terakhir_dijalankan,
                'port_saat_ini' => $i->bacaEnv('SERVER_PORT'), // VIEW ONLY - dibaca langsung dari .env
                'license_key_terisi' => filled($i->bacaEnv('SERVER_SECRET_LICENSE_KEY')),
                'db_terisi' => filled($i->db_host) && filled($i->db_name),
            ];
        });

        return Inertia::render('SuperAdmin/ExoInstances', ['instances' => $instances]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'slug' => 'required|string|max:50|unique:exo_instances,slug|alpha_dash',
            'path' => 'required|string|max:255',
            'db_host' => 'nullable|string|max:255',
            'db_port' => 'nullable|string|max:10',
            'db_name' => 'nullable|string|max:100',
            'db_user' => 'nullable|string|max:100',
            'db_pass' => 'nullable|string|max:255',
        ]);

        abort_unless(file_exists(rtrim($data['path'], '/') . '/.env'), 422, 'File .env tidak ditemukan di path itu. Cek lagi path-nya.');

        ExoInstance::create($data);

        return back()->with('success', 'Instance berhasil ditambahkan.');
    }

    public function updateDbCreds(Request $request, ExoInstance $exoInstance)
    {
        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|string|max:10',
            'db_name' => 'required|string|max:100',
            'db_user' => 'required|string|max:100',
            'db_pass' => 'required|string|max:255',
        ]);

        $exoInstance->update($data);

        return back()->with('success', "Kredensial database {$exoInstance->nama} disimpan.");
    }

    public function testConnection(ExoInstance $exoInstance)
    {
        $hasil = $exoInstance->tesKoneksiDb();

        return back()->with($hasil['ok'] ? 'success' : 'error', $hasil['pesan']);
    }

    public function updateLicenseKey(Request $request, ExoInstance $exoInstance)
    {
        $data = $request->validate(['license_key' => 'required|string|max:500']);

        $ok = $exoInstance->tulisEnv('SERVER_SECRET_LICENSE_KEY', $data['license_key']);
        abort_unless($ok, 500, 'Gagal menulis .env - cek izin file di server.');

        return back()->with('success', "License Key untuk {$exoInstance->nama} berhasil disimpan.");
    }

    /**
     * Jalankan main-amd64 di background (nohup). SENGAJA tanpa sudo - kalau
     * butuh sudo, harus dikonfigurasi passwordless sudo khusus command ini
     * di /etc/sudoers oleh admin server, gak bisa diatur dari sini.
     */
    public function run(ExoInstance $exoInstance)
    {
        $path = rtrim($exoInstance->path, '/');
        abort_unless(file_exists("{$path}/main-amd64"), 404, 'Binary main-amd64 tidak ditemukan di path instance ini.');

        // nohup ./main-amd64 > nohup.out 2>&1 & disown - dijalankan via bash -c
        // biar redirect & background process-nya kepakai dgn benar.
        $result = Process::path($path)->timeout(5)->start('bash -c "nohup ./main-amd64 > nohup.out 2>&1 & disown"');
        sleep(1); // kasih waktu proses mulai sebelum kita cek

        $exoInstance->update(['terakhir_dijalankan' => now()]);

        return back()->with('success', "Perintah jalankan {$exoInstance->nama} sudah dikirim. Cek status/log manual di server kalau perlu (nohup.out di {$path}).");
    }

    public function destroy(ExoInstance $exoInstance)
    {
        $exoInstance->delete();
        return back()->with('success', 'Instance dihapus dari daftar (file di server tidak ikut terhapus).');
    }
}
