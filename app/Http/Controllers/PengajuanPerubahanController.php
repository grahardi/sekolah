<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengajuanPerubahan;
use App\Models\Siswa;
use App\Models\WaliKelas;
use Illuminate\Http\Request;

class PengajuanPerubahanController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::where('status', 'aktif')->with('pengajuanPerubahan');

        $waliKelasSaya = null;
        if (! auth()->user()->isAdmin()) {
            $kelasWaliList = $this->kelasRombelWaliGuru();
            if ($kelasWaliList->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($kelasWaliList) {
                    foreach ($kelasWaliList as $kw) {
                        $q->orWhere(fn ($q2) => $q2->where('kelas', $kw->kelas)->where('rombel', $kw->rombel));
                    }
                });
                // Ambil record WaliKelas asli (bukan cuma kelas/rombel) buat tampilkan token-nya
                $guru = Guru::where('user_id', auth()->id())->first();
                $waliKelasSaya = WaliKelas::where('guru_id', $guru->id)
                    ->whereHas('tahunAjaran', fn ($q) => $q->where('is_aktif', true))
                    ->first();
            }
        }

        if ($request->filled('kelas_rombel')) {
            [$kelasFilter, $rombelFilter] = array_pad(explode('|', $request->kelas_rombel), 2, null);
            $query->where('kelas', $kelasFilter)->where('rombel', $rombelFilter ?: null);
        }

        // Statistik ngikutin filter kelas yg aktif (kalau ada) - dihitung dari
        // clone query SEBELUM pagination, biar akurat sesuai apa yg lagi
        // ditampilkan, bukan dari SEMUA siswa selalu.
        $semuaUntukStat = (clone $query)->with('pengajuanPerubahan')->get();
        $stats = [
            'total' => $semuaUntukStat->count(),
            'belum_isi' => $semuaUntukStat->filter(fn ($s) => ($s->pengajuanPerubahan?->status ?? 'belum_isi') === 'belum_isi')->count(),
            'menunggu_approval' => $semuaUntukStat->filter(fn ($s) => $s->pengajuanPerubahan?->status === 'menunggu_approval')->count(),
            'sudah_approve' => $semuaUntukStat->filter(fn ($s) => $s->pengajuanPerubahan?->status === 'sudah_approve')->count(),
            'tidak_ada_perubahan' => $semuaUntukStat->filter(fn ($s) => $s->pengajuanPerubahan?->status === 'tidak_ada_perubahan')->count(),
        ];
        // "Sudah Mengisi" gabungan - siapa saja yg SUDAH pernah submit form,
        // apapun hasilnya (disetujui, masih nunggu, atau memang gak ada yg
        // diubah) - beda dari "Belum Mengisi" yg blm disentuh sama sekali.
        $stats['sudah_mengisi'] = $stats['menunggu_approval'] + $stats['sudah_approve'] + $stats['tidak_ada_perubahan'];

        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, [20, 30, 50, 100])) $perPage = 20;

        $siswaList = $query->orderBy('kelas')->orderBy('rombel')->orderBy('nis')->orderBy('nama_lengkap')
            ->paginate($perPage)->withQueryString();

        $kelasRombelList = Siswa::where('status', 'aktif')->whereNotNull('kelas')
            ->get(['kelas', 'rombel'])
            ->map(fn ($s) => $s->rombel ? "{$s->kelas}|{$s->rombel}" : "{$s->kelas}|")
            ->unique()->sort()->values();

        $npsn = auth()->user()->sekolah->npsn;

        return view('pengajuan-perubahan.index', compact('siswaList', 'waliKelasSaya', 'npsn', 'kelasRombelList', 'perPage', 'stats'));
    }

    public function show(Siswa $siswa)
    {
        $this->pastikanBolehAkses($siswa);

        $pengajuan = PengajuanPerubahan::buatAtauAmbilUntuk($siswa);

        return view('pengajuan-perubahan.show', compact('siswa', 'pengajuan'));
    }

    public function proses(Request $request, Siswa $siswa)
    {
        $this->pastikanBolehAkses($siswa);

        $request->validate([
            'yakin' => 'required|accepted',
            'fields' => 'nullable|array',
            'fields.*' => 'in:' . implode(',', \App\Models\PengajuanPerubahan::FIELD_BOLEH_DIAJUKAN),
        ], [
            'yakin.required' => 'Centang dulu konfirmasi "Saya yakin" sebelum menyimpan.',
            'yakin.accepted' => 'Centang dulu konfirmasi "Saya yakin" sebelum menyimpan.',
        ]);

        $pengajuan = PengajuanPerubahan::where('siswa_id', $siswa->id)->firstOrFail();
        $usulan = $pengajuan->data_perubahan ?? [];

        $dataUpdate = [];
        foreach ($request->input('fields', []) as $field) {
            if (array_key_exists($field, $usulan)) {
                $dataUpdate[$field] = $usulan[$field];
            }
        }

        if (! empty($dataUpdate)) {
            $siswa->update($dataUpdate);
        }

        $pengajuan->update([
            'status' => 'sudah_approve',
            'diproses_oleh_user_id' => auth()->id(),
            'diproses_at' => now(),
        ]);

        return redirect()->route('pengajuan-perubahan.index')->with('success', count($dataUpdate) . ' field berhasil diperbarui & pengajuan ditandai selesai.');
    }

    /** Generate ulang token kelas (utk wali kelas yg login) */
    public function generateUlangToken()
    {
        abort_if(auth()->user()->isAdmin(), 400, 'Admin gak punya kelas wali sendiri - kelola token lewat menu Manajemen Sekolah.');

        $guru = Guru::where('user_id', auth()->id())->first();
        $waliKelas = WaliKelas::where('guru_id', $guru?->id)
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_aktif', true))
            ->first();

        abort_unless($waliKelas, 404, 'Anda bukan wali kelas aktif.');

        $waliKelas->update(['token' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6))]);

        return back()->with('success', 'Token baru berhasil dibuat.');
    }

    private function kelasRombelWaliGuru()
    {
        $guru = Guru::where('user_id', auth()->id())->first();
        if (! $guru) return collect();

        return WaliKelas::where('guru_id', $guru->id)
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_aktif', true))
            ->get(['kelas', 'rombel']);
    }

    /** Admin: lihat token seluruh kelas (bukan cuma kelas sendiri) */
    public function manajemenToken()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $waliKelasList = WaliKelas::with('guru')
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_aktif', true))
            ->orderBy('kelas')->orderBy('rombel')
            ->get();

        $npsn = auth()->user()->sekolah->npsn;

        return view('pengajuan-perubahan.manajemen-token', compact('waliKelasList', 'npsn'));
    }

    /** Admin: generate ulang token utk kelas tertentu (bukan cuma kelas sendiri) */
    public function generateUlangTokenAdmin(WaliKelas $waliKelas)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $waliKelas->update(['token' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6))]);

        return back()->with('success', "Token baru untuk kelas {$waliKelas->kelas_lengkap} berhasil dibuat.");
    }

    private function pastikanBolehAkses(Siswa $siswa): void
    {
        if (auth()->user()->isAdmin()) return;

        $bolehAkses = $this->kelasRombelWaliGuru()
            ->contains(fn ($kw) => $kw->kelas === $siswa->kelas && $kw->rombel === $siswa->rombel);

        abort_unless($bolehAkses, 403, 'Anda hanya bisa memproses pengajuan siswa di kelas yang Anda wali-i.');
    }
}
