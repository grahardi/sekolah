<?php

namespace App\Http\Controllers;

use App\Models\ScanKkHasil;
use App\Models\Siswa;
use App\Services\GeminiOcrService;
use Illuminate\Http\Request;

class ScanKkController extends Controller
{
    public function index()
    {
        $siswaList = Siswa::where('status', 'aktif')
            ->with('arsipBerkas', 'scanKkHasil')
            ->orderBy('kelas')->orderBy('rombel')->orderBy('nama_lengkap')
            ->get();

        $kelasRombelList = $siswaList->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();

        return view('siswa.scan-kk.index', compact('siswaList', 'kelasRombelList'));
    }

    /** Proses scan bulk - SKIP siswa yg sudah pernah discan (hemat kuota API), kecuali dipaksa ulang */
    public function scanBulk(Request $request)
    {
        $request->validate(['siswa_ids' => 'required|array|min:1']);
        $paksaUlang = $request->boolean('paksa_ulang');

        $sekolah = auth()->user()->sekolah;
        $service = new GeminiOcrService($sekolah);

        $siswaList = Siswa::whereIn('id', $request->siswa_ids)->with('arsipBerkas')->get();

        $berhasil = 0; $dilewati = 0; $gagal = 0; $tanpaFile = 0;

        foreach ($siswaList as $siswa) {
            $hasil = ScanKkHasil::firstOrNew(['siswa_id' => $siswa->id]);

            if ($hasil->exists && $hasil->sudahDiscan() && ! $paksaUlang) {
                $dilewati++;
                continue;
            }

            $arsip = $siswa->arsipBerkas;
            $adaFile = false;

            if ($arsip?->kartu_keluarga) {
                $adaFile = true;
                try {
                    $dataKk = $service->scanKk($arsip->kartu_keluarga, $siswa->nama_lengkap);
                    $hasil->status_kk = 'ok';
                    $hasil->skor_kk = (int) ($dataKk['skor_kejelasan'] ?? 0);
                    $hasil->data_kk = $dataKk;
                } catch (\Throwable $e) {
                    $hasil->status_kk = 'error';
                    $hasil->pesan_error = $e->getMessage();
                }
            }

            if ($arsip?->akta_lahir) {
                $adaFile = true;
                try {
                    $dataAkta = $service->scanAkta($arsip->akta_lahir);
                    $hasil->status_akta = 'ok';
                    $hasil->skor_akta = (int) ($dataAkta['skor_kejelasan_akta'] ?? 0);
                    $hasil->no_akta = $dataAkta['nomor_registrasi_akta'] ?? null;
                    $hasil->data_akta = $dataAkta;
                } catch (\Throwable $e) {
                    $hasil->status_akta = 'error';
                    $hasil->pesan_error = ($hasil->pesan_error ? $hasil->pesan_error . ' | ' : '') . $e->getMessage();
                }
            }

            if (! $adaFile) {
                $tanpaFile++;
                continue;
            }

            $hasil->siswa_id = $siswa->id;
            $hasil->discan_at = now();
            $hasil->save();

            if ($hasil->status_kk === 'error' || $hasil->status_akta === 'error') {
                $gagal++;
            } else {
                $berhasil++;
            }
        }

        $pesan = "{$berhasil} berhasil discan";
        if ($dilewati) $pesan .= ", {$dilewati} dilewati (sudah pernah discan)";
        if ($tanpaFile) $pesan .= ", {$tanpaFile} tidak ada berkas KK/Akta";
        if ($gagal) $pesan .= ", {$gagal} gagal";

        return back()->with($gagal > 0 ? 'warning' : 'success', $pesan . '.');
    }

    public function show(Siswa $siswa)
    {
        $hasil = ScanKkHasil::where('siswa_id', $siswa->id)->first()
            ?? new ScanKkHasil(['siswa_id' => $siswa->id, 'status_kk' => 'belum', 'status_akta' => 'belum']);

        return view('siswa.scan-kk.show', compact('siswa', 'hasil'));
    }

    /** Terapkan hasil OCR (nama ayah/ibu) ke data induk siswa */
    public function terapkan(Request $request, Siswa $siswa)
    {
        $request->validate([
            'field' => 'required|in:nama_ayah,nama_ibu',
            'nilai' => 'required|string|max:150',
        ]);

        $siswa->update([$request->field => $request->nilai]);

        return back()->with('success', 'Data induk berhasil diperbarui dari hasil scan.');
    }
}
