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

        return Inertia::render('SuperAdmin/ExoInstances', [
            'instances' => $instances,
            'masterSqlTersedia' => $this->masterSqlTersedia(),
        ]);
    }

    // Folder dasar tempat semua instance Extraordinary di-extract - scan
    // otomatis subfolder di sini utk dropdown pilih path, bukan ketik manual.
    private const BASE_PATH = '/home/aginza/sekolah';

    public function scanFolder(Request $request)
    {
        $basePath = $request->input('base_path', self::BASE_PATH);

        if (! is_dir($basePath)) {
            return response()->json(['error' => "Folder {$basePath} tidak ditemukan."], 404);
        }

        $pathTerpakai = ExoInstance::pluck('path', 'nama')->flip(); // path => nama, buat lookup cepat

        $folders = collect(scandir($basePath))
            ->reject(fn ($f) => in_array($f, ['.', '..']))
            ->filter(fn ($f) => is_dir("{$basePath}/{$f}"))
            ->map(function ($f) use ($basePath, $pathTerpakai) {
                $fullPath = "{$basePath}/{$f}";
                $adaEnv = file_exists("{$fullPath}/.env");
                $adaBinary = file_exists("{$fullPath}/main-amd64");

                return [
                    'nama_folder' => $f,
                    'path' => $fullPath,
                    'valid' => $adaEnv && $adaBinary,
                    'catatan' => ! $adaEnv ? 'Tidak ada .env' : (! $adaBinary ? 'Tidak ada main-amd64' : null),
                    'sudah_dipakai' => $pathTerpakai->has($fullPath) ? $pathTerpakai->get($fullPath) : null,
                ];
            })
            ->values();

        return response()->json(['folders' => $folders, 'base_path' => $basePath]);
    }

    public function uploadMasterSql(Request $request)
    {
        $request->validate(['master_sql' => 'required|file|max:51200']); // maks 50MB

        $path = $request->file('master_sql')->storeAs('exo', 'master.sql', 'local');

        return back()->with('success', 'File SQL master berhasil diupload dan siap dipakai utk provisioning instance baru.');
    }

    private function masterSqlTersedia(): bool
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->exists('exo/master.sql');
    }

    /**
     * Provisioning otomatis: buat database+user Postgres baru (pakai
     * password root Postgres yg diketik admin SAAT ITU JUGA, tidak pernah
     * disimpan ke database sama sekali), jalankan SQL master, tentukan
     * port acak, lalu tulis semuanya ke .env di path yg sudah ditentukan.
     */
    private function provisionOtomatis(array $data, string $rootPassword): array
    {
        $dbName = 'exo_' . $data['slug'];
        $dbUser = 'exo_' . $data['slug'];
        $dbPass = \Illuminate\Support\Str::random(24);

        $portTerpakai = ExoInstance::pluck('port')->filter()->map(fn ($p) => (int) $p)->toArray();
        do {
            $port = rand(12000, 13000);
        } while (in_array($port, $portTerpakai));

        // 1. Konek sbg postgres superuser via PDO (password cuma dipakai
        //    sekali di request ini, gak pernah ditulis ke storage/DB kita).
        try {
            $pdo = new \PDO("pgsql:host=localhost;port=5432;dbname=postgres", 'postgres', $rootPassword);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Throwable $e) {
            return ['ok' => false, 'pesan' => 'Password root Postgres salah atau gagal konek: ' . $e->getMessage()];
        }

        try {
            $pdo->exec("CREATE USER \"{$dbUser}\" WITH PASSWORD " . $pdo->quote($dbPass));
            $pdo->exec("CREATE DATABASE \"{$dbName}\" OWNER \"{$dbUser}\"");
        } catch (\Throwable $e) {
            return ['ok' => false, 'pesan' => 'Gagal membuat user/database: ' . $e->getMessage()];
        }

        // 2. Jalankan SQL master ke database baru - pakai psql via shell,
        //    krn file dump bisa berisi sintaks kompleks (COPY, fungsi, dll)
        //    yg gak reliable dieksekusi lewat PDO::exec() biasa.
        $masterSqlPath = storage_path('app/exo/master.sql');
        $prosesImport = \Illuminate\Support\Facades\Process::timeout(120)
            ->env(['PGPASSWORD' => $rootPassword])
            ->run(['psql', '-h', 'localhost', '-U', 'postgres', '-d', $dbName, '-f', $masterSqlPath]);

        if (! $prosesImport->successful()) {
            return ['ok' => false, 'pesan' => 'Database dibuat, tapi gagal import SQL master: ' . $prosesImport->errorOutput()];
        }

        // 3. Tulis .env di path instance
        $envPath = rtrim($data['path'], '/') . '/.env';
        if (! file_exists($envPath)) {
            return ['ok' => false, 'pesan' => "Database & SQL sukses, tapi file .env tidak ditemukan di {$envPath} - pastikan foldernya sudah berisi hasil extract Extraordinary CBT dulu."];
        }

        $instanceSementara = new ExoInstance(['path' => $data['path']]);
        $instanceSementara->tulisEnv('DB_HOST', 'localhost');
        $instanceSementara->tulisEnv('DB_PORT', '5432');
        $instanceSementara->tulisEnv('DB_NAME', $dbName);
        $instanceSementara->tulisEnv('DB_USER', $dbUser);
        $instanceSementara->tulisEnv('DB_PASS', $dbPass);
        $instanceSementara->tulisEnv('SERVER_PORT', (string) $port);

        return [
            'ok' => true,
            'pesan' => 'Provisioning berhasil.',
            'db_host' => 'localhost', 'db_port' => '5432',
            'db_name' => $dbName, 'db_user' => $dbUser, 'db_pass' => $dbPass,
            'port' => (string) $port,
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'slug' => 'required|string|max:50|unique:exo_instances,slug|alpha_dash',
            'path' => 'required|string|max:255',
            'provision_otomatis' => 'nullable|boolean',
            'db_root_password' => 'required_if:provision_otomatis,1|nullable|string',
        ]);

        abort_unless(file_exists(rtrim($data['path'], '/') . '/.env'), 422, 'File .env tidak ditemukan di path itu. Pastikan folder Extraordinary CBT sudah di-extract dulu di path tsb.');

        if ($request->boolean('provision_otomatis')) {
            abort_unless($this->masterSqlTersedia(), 422, 'Upload file SQL master dulu sebelum provisioning otomatis.');

            $hasil = $this->provisionOtomatis($data, $data['db_root_password']);
            abort_unless($hasil['ok'], 422, $hasil['pesan']);

            $data = array_merge($data, [
                'db_host' => $hasil['db_host'], 'db_port' => $hasil['db_port'],
                'db_name' => $hasil['db_name'], 'db_user' => $hasil['db_user'], 'db_pass' => $hasil['db_pass'],
                'port' => $hasil['port'],
            ]);
        }

        // db_root_password SENGAJA tidak pernah masuk ke ExoInstance::create()
        // - cuma dipakai sekali di request ini utk provisioning, lalu dibuang.
        unset($data['provision_otomatis'], $data['db_root_password']);
        ExoInstance::create($data);

        return back()->with('success', $request->boolean('provision_otomatis')
            ? 'Instance berhasil dibuat & database di-provisioning otomatis!'
            : 'Instance berhasil ditambahkan.');
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
