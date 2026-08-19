<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\PengajuanPerubahan;
use App\Models\Siswa;
use App\Models\WaliKelas;
use Illuminate\Http\Request;

class PengajuanPerubahanController extends Controller
{
    public function index()
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

        $siswaList = $query->orderBy('kelas')->orderBy('rombel')->orderBy('nama_lengkap')->get();
        $npsn = auth()->user()->sekolah->npsn;

        return view('pengajuan-perubahan.index', compact('siswaList', 'waliKelasSaya', 'npsn'));
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

    private function pastikanBolehAkses(Siswa $siswa): void
    {
        if (auth()->user()->isAdmin()) return;

        $bolehAkses = $this->kelasRombelWaliGuru()
            ->contains(fn ($kw) => $kw->kelas === $siswa->kelas && $kw->rombel === $siswa->rombel);

        abort_unless($bolehAkses, 403, 'Anda hanya bisa memproses pengajuan siswa di kelas yang Anda wali-i.');
    }
}
