<?php

namespace App\Http\Controllers;

use App\Models\ScanKkHasil;
use App\Models\Siswa;
use App\Services\GeminiOcrService;
use Illuminate\Http\Request;

class ScanKkController extends Controller
{
    private const FIELD_BISA_DITERAPKAN = 'nama_ayah,nama_ibu,nik_ayah,nik_ibu,tahun_lahir_ayah,tahun_lahir_ibu,pekerjaan_ayah,pekerjaan_ibu,alamat,anak_ke,nama_lengkap,nik,tanggal_lahir,no_kk';

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
        $hasil->dikonfirmasi_at = null; // data baru, perlu dikonfirmasi ulang
        $hasil->dikonfirmasi_oleh_user_id = null;
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

    /** Scan langsung 1 siswa (dari halaman detailnya sendiri) - beda dgn scanBulk yg proses banyak siswa sekaligus */
    public function scanSatu(Siswa $siswa)
    {
        $sekolah = auth()->user()->sekolah;
        $service = new GeminiOcrService($sekolah);

        $hasil = ScanKkHasil::firstOrNew(['siswa_id' => $siswa->id]);
        $arsip = $siswa->arsipBerkas;

        if (! $arsip?->kartu_keluarga && ! $arsip?->akta_lahir) {
            return back()->with('error', 'Belum ada berkas KK atau Akta Lahir untuk siswa ini.');
        }

        if ($arsip->kartu_keluarga) {
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

        if ($arsip->akta_lahir) {
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

        $hasil->siswa_id = $siswa->id;
        $hasil->discan_at = now();
        $hasil->dikonfirmasi_at = null; // data baru, perlu dikonfirmasi ulang
        $hasil->dikonfirmasi_oleh_user_id = null;
        $hasil->save();

        $sukses = $hasil->status_kk !== 'error' && $hasil->status_akta !== 'error';

        return redirect()->route('siswa.scan-kk.show', $siswa)
            ->with($sukses ? 'success' : 'error', $sukses ? 'Scan berhasil.' : 'Scan selesai tapi ada yg error: ' . $hasil->pesan_error);
    }

    /** Terapkan hasil OCR (nama ayah/ibu) ke data induk siswa */
    public function terapkan(Request $request, Siswa $siswa)
    {
        $request->validate([
            'field' => 'required|in:' . self::FIELD_BISA_DITERAPKAN,
            'nilai' => 'required|string|max:150',
        ]);

        $siswa->update([$request->field => $request->nilai]);
        $this->tandaiSudahDikonfirmasi($siswa);

        return back()->with('success', 'Data induk berhasil diperbarui dari hasil scan.');
    }

    /** Terapkan banyak field sekaligus (centang massal) */
    public function terapkanMassal(Request $request, Siswa $siswa)
    {
        $request->validate([
            'fields' => 'nullable|array',
            'fields.*' => 'in:' . self::FIELD_BISA_DITERAPKAN,
            'nilai' => 'nullable|array',
        ]);

        if (empty($request->fields)) {
            return back()->with('error', 'Belum ada perubahan yang dicentang. Kalau memang tidak ada yang perlu diubah, pakai tombol "Update Status Saja".');
        }

        $dataUpdate = [];
        foreach ($request->fields as $field) {
            if (array_key_exists($field, $request->nilai ?? []) && $request->nilai[$field] !== '') {
                $dataUpdate[$field] = $request->nilai[$field];
            }
        }

        if (empty($dataUpdate)) {
            return back()->with('error', 'Tidak ada field yang dicentang.');
        }

        $siswa->update($dataUpdate);
        $this->tandaiSudahDikonfirmasi($siswa);

        return back()->with('success', count($dataUpdate) . ' field berhasil diperbarui dari hasil scan.');
    }

    /** Konfirmasi tanpa ubah data apa pun - dipakai kalau semua field hasil scan sudah sama persis dgn data induk */
    public function tandaiUpdate(Siswa $siswa)
    {
        $this->tandaiSudahDikonfirmasi($siswa);

        return back()->with('success', 'Data ditandai sudah diperiksa & sesuai.');
    }

    private function tandaiSudahDikonfirmasi(Siswa $siswa): void
    {
        ScanKkHasil::where('siswa_id', $siswa->id)->update([
            'dikonfirmasi_at' => now(),
            'dikonfirmasi_oleh_user_id' => auth()->id(),
        ]);
    }
}
