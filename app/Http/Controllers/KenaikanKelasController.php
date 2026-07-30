<?php

namespace App\Http\Controllers;

use App\Models\{Siswa, RiwayatKelas};
use Illuminate\Http\Request;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        $kelasList   = Siswa::where('status', 'aktif')
                            ->select('kelas')->distinct()->orderBy('kelas')
                            ->pluck('kelas');
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

        return view('kenaikan.index', compact('kelasList', 'tahunAjaran'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'kelas'        => 'required|string|max:10',
            'aksi'         => 'required|in:naik,lulus,tinggal,pindah,keluar',
            'tahun_ajaran' => 'required|string|max:10',
            'kelas_tujuan' => 'nullable|string|max:10',
            'wali_kelas'   => 'nullable|string|max:100',
        ]);

        $siswas = Siswa::where('kelas', $request->kelas)
                       ->where('status', 'aktif')
                       ->orderBy('nama_lengkap')
                       ->get();

        $rombelTujuan = [];
        foreach ($siswas as $s) {
            $rombelTujuan[$s->id] = $this->hitungRombelTujuan($s, $request->aksi, $request->kelas_tujuan);
        }

        return view('kenaikan.preview', [
            'siswas'       => $siswas,
            'kelas'        => $request->kelas,
            'aksi'         => $request->aksi,
            'tahun_ajaran' => $request->tahun_ajaran,
            'kelas_tujuan' => $request->kelas_tujuan,
            'wali_kelas'   => $request->wali_kelas,
            'rombelTujuan' => $rombelTujuan,
        ]);
    }

    public function proses(Request $request)
    {
        $request->validate([
            'siswa_ids'    => 'required|array|min:1',
            'siswa_ids.*'  => 'exists:siswas,id',
            'aksi'         => 'required|in:naik,lulus,tinggal,pindah,keluar',
            'kelas_asal'   => 'required|string|max:10',
            'tahun_ajaran' => 'required|string|max:10',
            'kelas_tujuan' => 'nullable|string|max:10',
            'wali_kelas'   => 'nullable|string|max:100',
        ]);

        $ids         = $request->siswa_ids;
        $aksi        = $request->aksi;
        $kelasAsal   = $request->kelas_asal;
        $ta          = $request->tahun_ajaran;
        $kelasTujuan = $request->kelas_tujuan;
        $waliKelas   = $request->wali_kelas;

        $hasilMap = [
            'naik'    => ['hasil' => 'Naik Kelas',   'status' => 'aktif'],
            'lulus'   => ['hasil' => 'Lulus',         'status' => 'lulus'],
            'tinggal' => ['hasil' => 'Tinggal Kelas', 'status' => 'aktif'],
            'pindah'  => ['hasil' => 'Pindah',        'status' => 'pindah'],
            'keluar'  => ['hasil' => 'Keluar',        'status' => 'keluar'],
        ];

        $hasil      = $hasilMap[$aksi]['hasil'];
        $statusBaru = $hasilMap[$aksi]['status'];
        $berhasil   = 0;
        $errors     = [];

        foreach ($ids as $id) {
            try {
                $siswa = Siswa::findOrFail($id);

                RiwayatKelas::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $ta],
                    [
                        'kelas'      => $kelasAsal,
                        'rombel'     => $siswa->rombel,
                        'wali_kelas' => $waliKelas,
                        'hasil'      => $hasil,
                    ]
                );

                [$kelasBaru, $rombelBaru] = $this->hitungKelasRombelBaru($siswa, $aksi, $kelasTujuan);

                $siswa->update([
                    'status' => $statusBaru,
                    'kelas'  => $kelasBaru,
                    'rombel' => $rombelBaru,
                ]);

                $berhasil++;

            } catch (\Throwable $e) {
                $errors[] = "ID {$id}: " . $e->getMessage();
            }
        }

        $msg = match($aksi) {
            'naik'    => "{$berhasil} siswa berhasil dinaikkan ke kelas {$kelasTujuan}.",
            'lulus'   => "{$berhasil} siswa berhasil diluluskan.",
            'tinggal' => "{$berhasil} siswa ditetapkan tinggal kelas.",
            'pindah'  => "{$berhasil} siswa ditandai pindah sekolah.",
            'keluar'  => "{$berhasil} siswa ditandai keluar.",
        };

        if (count($errors) > 0) $msg .= ' ' . count($errors) . ' gagal.';

        return redirect()->route('kenaikan.index')
            ->with(count($errors) > 0 ? 'warning' : 'success', $msg)
            ->with('proses_errors', $errors);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function hitungKelasRombelBaru(Siswa $siswa, string $aksi, ?string $kelasTujuan): array
    {
        $kelasAsal  = $siswa->kelas;
        $rombelAsal = $siswa->rombel;

        switch ($aksi) {
            case 'naik':
                $kelasBaru  = $kelasTujuan ?? $kelasAsal;
                $rombelBaru = $this->gantiAngkaRombel($rombelAsal, $kelasAsal, $kelasBaru);
                return [$kelasBaru, $rombelBaru];

            case 'lulus':
                $rombelBaru = $rombelAsal ? 'Alumni ' . $rombelAsal : 'Alumni ' . $kelasAsal;
                return [$kelasAsal, $rombelBaru];

            case 'tinggal':
            case 'pindah':
            case 'keluar':
            default:
                return [$kelasAsal, $rombelAsal];
        }
    }

    /**
     * Ganti angka/romawi kelas di string rombel, pertahankan huruf.
     * "7A" → "8A" | "VII A" → "VIII A" | "8B" → "9B"
     */
    private function gantiAngkaRombel(?string $rombel, string $kelasAsal, string $kelasTujuan): ?string
    {
        if (!$rombel) return null;

        $romawiKeArab = ['VII' => '7', 'VIII' => '8', 'IX' => '9'];
        $asalArab = $romawiKeArab[$kelasAsal]  ?? null;
        $tujuArab = $romawiKeArab[$kelasTujuan] ?? null;

        if ($asalArab && $tujuArab) {
            $hasil = preg_replace('/\b' . preg_quote($asalArab, '/') . '\b/', $tujuArab, $rombel);
            if ($hasil !== $rombel) return $hasil;
        }

        $hasil = preg_replace('/\b' . preg_quote($kelasAsal, '/') . '\b/', $kelasTujuan, $rombel);
        if ($hasil !== $rombel) return $hasil;

        return $rombel;
    }

    public function hitungRombelTujuan(Siswa $siswa, string $aksi, ?string $kelasTujuan): string
    {
        [$kb, $rb] = $this->hitungKelasRombelBaru($siswa, $aksi, $kelasTujuan);
        if ($aksi === 'lulus') return $rb ?? ('Alumni ' . $siswa->kelas);
        return $rb ?? $kb;
    }
}
