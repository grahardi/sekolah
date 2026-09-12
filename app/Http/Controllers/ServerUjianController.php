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
        $mode = $request->input('mode', 'update') === 'reset' ? 'reset' : 'update';
        $sertakanFoto = $request->boolean('sertakan_foto');
        $hasil = \App\Services\ExoSyncService::sinkronSiswa($instance, $identifier, $mode, $sertakanFoto);

        return back()->with($hasil['ok'] ? 'success' : 'error', $hasil['pesan']);
    }

    public function exportDataSiswa(ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);
        abort_unless($instance->db_host, 422, 'Kredensial database instance belum diisi.');

        $conn = $instance->dbConnection();

        $peserta = $conn->table('pesertas')
            ->leftJoin('group_members', 'pesertas.id', '=', 'group_members.student_id')
            ->leftJoin('groups', 'group_members.group_id', '=', 'groups.id')
            ->select('pesertas.no_ujian', 'pesertas.nama', 'pesertas.password', 'groups.name as group_name')
            ->orderBy('groups.name')
            ->orderBy('pesertas.nama')
            ->get();

        if ($peserta->isEmpty()) {
            return back()->with('error', 'Belum ada data siswa tersinkron di Server Ujian ini. Sinkron dulu sebelum export.');
        }

        // Agama diambil dari data induk KITA (bukan reverse-map dari agama_id
        // di sisi Extraordinary) - lebih akurat & gak perlu peta balik ID->nama.
        $agamaMap = \App\Models\Siswa::withoutGlobalScopes()
            ->where('sekolah_id', $instance->sekolah_id)
            ->where('status', 'aktif')
            ->get()
            ->reduce(function ($map, $s) {
                if ($s->nisn) $map[$s->nisn] = $s->agama;
                if ($s->nis) $map[$s->nis] = $s->agama;
                return $map;
            }, []);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['No. Ujian (Username)', 'Nama', 'Password', 'Grup / Kelas', 'Agama']);

        $baris = 2;
        foreach ($peserta as $p) {
            $sheet->fromArray([$p->no_ujian, $p->nama, $p->password, $p->group_name ?: '-', $agamaMap[$p->no_ujian] ?? '-'], null, "A{$baris}");
            $baris++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaFile = 'data-login-siswa-' . \Illuminate\Support\Str::slug($instance->nama_instance ?? 'server-ujian') . '.xlsx';
        $path = storage_path("app/{$namaFile}");
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function updateLicenseKey(Request $request, ExoInstance $instance)
    {
        abort_unless($instance->sekolah_id === auth()->user()->sekolah_id, 403);

        $data = $request->validate(['license_key' => 'required|string|max:255']);

        $ok = $instance->tulisEnv('SERVER_SECRET_LICENSE_KEY', $data['license_key']);

        if (! $ok) {
            return back()->with('error', 'Gagal menyimpan License Key - file .env instance tidak ditemukan.');
        }

        return back()->with('success', 'License Key berhasil disimpan. Restart Server Ujian (Hentikan lalu Jalankan lagi) supaya perubahan berlaku.');
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

    /**
     * Panel Pengawas Ujian & Monitoring Ruangan - DIINTEGRASIKAN LANGSUNG ke
     * Laravel (pakai layout Server Ujian sendiri, bukan lagi file panel.php/
     * pr.php/index.php berdiri sendiri dgn kredensial hardcode). Baca lewat
     * ExoInstance::dbConnection() - koneksi dinamis TERENKRIPSI per sekolah,
     * jadi tetap aman & terisolasi per instance walau kodenya digabung.
     */
    private function instanceSaya(): ExoInstance
    {
        $instance = ExoInstance::where('sekolah_id', auth()->user()->sekolah_id)->first();
        abort_unless($instance, 404, 'Server Ujian belum diaktifkan utk sekolah ini.');
        return $instance;
    }

    public function panelPengawas(Request $request)
    {
        $instance = $this->instanceSaya();
        $db = $instance->dbConnection();
        $view = $request->get('view', 'active');
        $port = $instance->bacaEnv('SERVER_PORT');
        $fotoBaseUrl = "http://163.227.0.18:{$port}/wormhole/";

        $tokenAktif = $db->table('tokens')->where('status', 1)->orderByDesc('created_at')->first();
        $isExpired = false;
        $createdAtWIB = $expiredAtWIB = null;
        if ($tokenAktif) {
            $nowUTC = new \DateTime('now', new \DateTimeZone('UTC'));
            $expiryUTC = new \DateTime($tokenAktif->expired_at, new \DateTimeZone('UTC'));
            $isExpired = $nowUTC > $expiryUTC;
            $createdAtWIB = (new \DateTime($tokenAktif->created_at, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            $expiredAtWIB = (new \DateTime($tokenAktif->expired_at, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        }

        $pesertas = $topBlocked = collect();

        if ($view === 'top_blocked') {
            // Skema asli ExoCBT gak punya tabel log blokir bertimestamp -
            // "percobaan keluar aplikasi" dilacak sbg COUNTER doang di tabel
            // devices (out_count) per peserta, bukan riwayat detail. Ranking
            // ini dibangun dari counter itu - paling mendekati konsep
            // "siapa yg paling sering dicurigai" dgn data yg BENERAN ada.
            $topBlocked = collect($db->select(
                "SELECT p.nama, p.no_ujian, SUM(d.out_count) as total_keluar, MAX(d.last_verified_at) as terakhir_aktif
                 FROM devices d
                 JOIN pesertas p ON p.id = d.peserta_id
                 WHERE d.out_count > 0
                 GROUP BY p.id, p.nama, p.no_ujian
                 ORDER BY total_keluar DESC
                 LIMIT 20"
            ));
        } else {
            $pesertas = collect($db->select(
                "SELECT p.nama, p.no_ujian, p.block_reason, p.blocked_at, p.ava,
                        COALESCE((SELECT SUM(d.out_count) FROM devices d WHERE d.peserta_id = p.id), 0) as total_keluar
                 FROM pesertas p WHERE p.status = 0 ORDER BY p.nama ASC"
            ));
        }

        return view('server-ujian.panel-pengawas', compact(
            'view', 'tokenAktif', 'isExpired', 'createdAtWIB', 'expiredAtWIB',
            'pesertas', 'topBlocked', 'instance', 'fotoBaseUrl'
        ));
    }

    public function panelPengawasAktifkan(Request $request)
    {
        $instance = $this->instanceSaya();
        $db = $instance->dbConnection();
        $nama = $request->input('nama');

        $db->table('pesertas')->where('nama', $nama)->update([
            'status' => 1, 'block_reason' => null, 'blocked_at' => null,
        ]);

        return back()->with('success', "Peserta \"{$nama}\" berhasil diaktifkan.");
    }

    public function monitoringRuangan(Request $request)
    {
        $instance = $this->instanceSaya();
        $db = $instance->dbConnection();
        $port = $instance->bacaEnv('SERVER_PORT');
        $fotoBaseUrl = "http://163.227.0.18:{$port}/wormhole/";

        $allJadwalAktif = collect($db->select("SELECT id, alias FROM jadwals WHERE status_ujian = 1"));
        $filterJadwal = $request->get('jadwal_id') ?: ($allJadwalAktif->first()->id ?? null);

        // Skema asli ExoCBT gak punya data kursi/ruang fisik (kartuujian dgn
        // baris/posisi TIDAK ADA di tabel manapun) - monitoring dibangun per
        // KELOMPOK (groups/group_members, hasil sinkron dari kelas-rombel
        // kita sendiri) drpd nge-ada2in tata kursi yg datanya emang gak ada.
        $subGrupList = collect($db->select(
            "SELECT g.id, g.name FROM groups g WHERE g.parent_id IS NOT NULL ORDER BY g.name"
        ));

        $filterGrup = $request->get('grup_id') ?: ($subGrupList->first()->id ?? null);

        $stats = ['mengerjakan' => 0, 'selesai' => 0, 'belum_login' => 0, 'total' => 0];
        $pesertaGrup = collect();

        if ($filterGrup) {
            $pesertaGrup = collect($db->select(
                "SELECT p.id, p.nama, p.no_ujian, p.ava, su.status_ujian, su.sisa_waktu
                 FROM group_members gm
                 JOIN pesertas p ON p.id = gm.student_id
                 LEFT JOIN siswa_ujians su ON su.peserta_id = p.id AND su.jadwal_id = ?
                 WHERE gm.group_id = ?
                 ORDER BY p.nama ASC",
                [$filterJadwal, $filterGrup]
            ));

            foreach ($pesertaGrup as $p) {
                $stats['total']++;
                if ($p->status_ujian === null) $stats['belum_login']++;
                elseif ((int) $p->status_ujian >= 2) $stats['selesai']++;
                else $stats['mengerjakan']++;
            }
        }

        return view('server-ujian.monitoring', compact(
            'allJadwalAktif', 'filterJadwal', 'subGrupList', 'filterGrup', 'pesertaGrup', 'stats', 'instance', 'fotoBaseUrl'
        ));
    }

    public function monitoringLaporError(Request $request)
    {
        // Fitur "lapor error" perlu tabel penampung khusus yg gak ada di
        // skema asli ExoCBT - sementara dicatat sbg catatan biasa via
        // block_reason drpd dihapus fiturnya sepenuhnya.
        $instance = $this->instanceSaya();
        $instance->dbConnection()->table('pesertas')
            ->where('no_ujian', $request->input('no_ujian_lapor'))
            ->update(['block_reason' => 'Dilaporkan kendala oleh pengawas']);

        return back()->with('success', 'Peserta ditandai mengalami kendala.');
    }
}
