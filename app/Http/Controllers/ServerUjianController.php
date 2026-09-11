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

        $tokenAktif = $isExpired = $createdAtWIB = $expiredAtWIB = null;
        $pesertas = $riwayat = $topBlocked = collect();
        $totalData = 0;
        $totalPages = 1;
        $page = max(1, (int) $request->get('page', 1));
        $errorSkema = null;

        try {
            $tokenAktif = $db->table('tokens')->where('status', 1)->orderByDesc('created_at')->first();
            $isExpired = false;
            if ($tokenAktif) {
                $nowUTC = new \DateTime('now', new \DateTimeZone('UTC'));
                $expiryUTC = new \DateTime($tokenAktif->expired_at, new \DateTimeZone('UTC'));
                $isExpired = $nowUTC > $expiryUTC;
                $createdAtWIB = (new \DateTime($tokenAktif->created_at, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                $expiredAtWIB = (new \DateTime($tokenAktif->expired_at, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            }

            if ($view === 'history') {
                $limit = 15;
                $offset = ($page - 1) * $limit;
                $totalData = $db->table('logblokir')->count();
                $totalPages = (int) ceil($totalData / $limit);

                $riwayat = collect($db->select(
                    "SELECT l.nama, l.alasan_blokir, l.jam_terblokir, l.jam_diaktifkan,
                            (SELECT COUNT(*) FROM logblokir l2 WHERE l2.nama = l.nama) as total_blokir
                     FROM logblokir l ORDER BY l.jam_diaktifkan DESC LIMIT ? OFFSET ?",
                    [$limit, $offset]
                ));
            } elseif ($view === 'top_blocked') {
                $topBlocked = collect($db->select(
                    "SELECT nama, COUNT(*) as total_pelanggaran, MAX(jam_diaktifkan) as terakhir_aktif
                     FROM logblokir GROUP BY nama ORDER BY total_pelanggaran DESC, terakhir_aktif DESC LIMIT 20"
                ));
            } else {
                // Coba dgn subquery logblokir dulu (versi skema lengkap) - kalau
                // tabelnya gak ada di instance ini (beda versi/instance demo),
                // fallback ke query TANPA logblokir drpd nge-crash total.
                try {
                    $pesertas = collect($db->select(
                        "SELECT p.nama, p.block_reason, p.blocked_at,
                                (SELECT COUNT(*) FROM logblokir l WHERE l.nama = p.nama) as total_blokir
                         FROM pesertas p WHERE p.status = 0 ORDER BY p.nama ASC"
                    ));
                } catch (\Illuminate\Database\QueryException $e) {
                    $pesertas = collect($db->select(
                        "SELECT p.nama, p.block_reason, p.blocked_at, 0 as total_blokir
                         FROM pesertas p WHERE p.status = 0 ORDER BY p.nama ASC"
                    ));
                    $errorSkema = 'Tabel riwayat blokir (logblokir) belum ada di instance ini - riwayat & ranking blokir belum bisa ditampilkan, tapi daftar terblokir saat ini tetap tampil.';
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorSkema = 'Gagal membaca data dari database instance ujian ini. Kemungkinan skema tabel belum lengkap atau instance belum pernah dipakai. Detail: ' . $e->getMessage();
        }

        return view('server-ujian.panel-pengawas', compact(
            'view', 'tokenAktif', 'isExpired', 'createdAtWIB', 'expiredAtWIB',
            'pesertas', 'riwayat', 'topBlocked', 'totalData', 'totalPages', 'page', 'instance', 'errorSkema'
        ));
    }

    public function panelPengawasAktifkan(Request $request)
    {
        $instance = $this->instanceSaya();
        $db = $instance->dbConnection();
        $nama = $request->input('nama');

        $peserta = $db->table('pesertas')->where('nama', $nama)->first();
        if ($peserta) {
            try {
                $db->table('logblokir')->insert([
                    'nama' => $peserta->nama,
                    'alasan_blokir' => $peserta->block_reason,
                    'jam_terblokir' => $peserta->blocked_at,
                    'jam_diaktifkan' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Tabel logblokir blm ada di instance ini - riwayat gak
                // tercatat, tapi tetap lanjut aktifkan pesertanya drpd stuck.
            }
            $db->table('pesertas')->where('nama', $nama)->update([
                'status' => 1, 'block_reason' => null, 'blocked_at' => null,
            ]);
        }

        return back()->with('success', "Peserta \"{$nama}\" berhasil diaktifkan.");
    }

    public function monitoringRuangan(Request $request)
    {
        $instance = $this->instanceSaya();
        $db = $instance->dbConnection();

        $filterRuang = (int) $request->get('ruang', 1);
        $filterJadwal = $request->get('jadwal_id');

        $allJadwalAktif = collect($db->select("SELECT id, alias FROM jadwals WHERE status_ujian = 1"));
        if (! $filterJadwal && $allJadwalAktif->isNotEmpty()) {
            $filterJadwal = $allJadwalAktif->first()->id;
        }

        $mapTipeRuang = [1=>'kanan',2=>'kanan',3=>'kanan',4=>'kanan',5=>'kanan',6=>'kiri',7=>'kiri',8=>'kanan',9=>'kanan',10=>'kanan',11=>'kanan',12=>'kanan',13=>'kiri',14=>'kiri',15=>'kiri',16=>'kiri',17=>'kiri',18=>'kiri',19=>'kiri',20=>'kiri'];
        $tipeRuangan = $mapTipeRuang[$filterRuang] ?? 'kanan';

        $mejaData = [];
        for ($i = 1; $i <= 16; $i++) $mejaData[$i] = ['A' => null, 'B' => null];
        $stats = ['mengerjakan' => 0, 'selesai' => 0, 'belum_login' => 0, 'error' => 0, 'total' => 0];

        $listData = $filterJadwal ? collect($db->select(
            "SELECT k.nama, k.no_ujian, k.kelas, k.foto, k.baris as no_meja, k.posisi, k.laporan,
                    su.status_ujian as status_kerja, su.sisa_waktu
             FROM kartuujian k
             LEFT JOIN pesertas p ON k.no_ujian = p.no_ujian
             LEFT JOIN siswa_ujians su ON p.id = su.peserta_id AND su.jadwal_id = ?
             WHERE k.ruang = ? ORDER BY k.baris ASC, k.posisi ASC",
            [$filterJadwal, $filterRuang]
        )) : collect();

        foreach ($listData as $d) {
            $m = (int) $d->no_meja;
            $p = strtoupper($d->posisi ?? 'A');
            if ($m >= 1 && $m <= 16) {
                $mejaData[$m][$p] = $d;
                $stats['total']++;
                if ($d->laporan == 1) {
                    $stats['error']++;
                } else {
                    $sk = $d->status_kerja;
                    if ($sk === null) $stats['belum_login']++;
                    elseif ($sk >= 2) $stats['selesai']++;
                    elseif ($sk == 1) $stats['mengerjakan']++;
                    else $stats['belum_login']++;
                }
            }
        }

        $matriks = [];
        for ($r = 0; $r < 4; $r++) {
            $temp = range(($r * 4) + 1, ($r + 1) * 4);
            if ($tipeRuangan == 'kanan') {
                $matriks[] = ($r % 2 == 0) ? array_reverse($temp) : $temp;
            } else {
                $matriks[] = ($r % 2 == 0) ? $temp : array_reverse($temp);
            }
        }

        return view('server-ujian.monitoring', compact(
            'filterRuang', 'filterJadwal', 'allJadwalAktif', 'tipeRuangan', 'mejaData', 'stats', 'matriks', 'instance'
        ));
    }

    public function monitoringLaporError(Request $request)
    {
        $instance = $this->instanceSaya();
        $instance->dbConnection()->table('kartuujian')->where('no_ujian', $request->input('no_ujian_lapor'))->update(['laporan' => 1]);

        return redirect()->back();
    }
}
