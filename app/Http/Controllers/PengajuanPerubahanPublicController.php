<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPerubahan;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\WaliKelas;
use Illuminate\Http\Request;

class PengajuanPerubahanPublicController extends Controller
{
    /** Halaman verifikasi (masukkan No. Induk + Tanggal Lahir + Token kelas) */
    public function verifikasi(string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();

        if (session('pengajuan_siswa_id')) {
            return redirect()->route('pengajuan-publik.form', $npsn);
        }

        return view('pengajuan-publik.verifikasi', compact('sekolah', 'npsn'));
    }

    public function prosesVerifikasi(Request $request, string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();

        $request->validate([
            'no_induk' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'token' => 'required|string',
        ]);

        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)
            ->where(function ($q) use ($request) {
                $q->where('nis', $request->no_induk)->orWhere('nisn', $request->no_induk);
            })
            ->first();

        $gagal = fn () => back()->withErrors(['error' => 'No. Induk, Tanggal Lahir, atau Token tidak cocok. Cek lagi data yang dimasukkan, atau hubungi wali kelas untuk token yang benar.'])->withInput();

        if (! $siswa) return $gagal();

        $tanggalCocok = $siswa->tanggal_lahir && $siswa->tanggal_lahir->format('Y-m-d') === $request->tanggal_lahir;
        if (! $tanggalCocok) return $gagal();

        $waliKelas = WaliKelas::where('sekolah_id', $sekolah->id)
            ->where('kelas', $siswa->kelas)->where('rombel', $siswa->rombel)
            ->whereHas('tahunAjaran', fn ($q) => $q->where('is_aktif', true))
            ->first();

        if (! $waliKelas || ! $waliKelas->token || ! hash_equals($waliKelas->token, strtoupper(trim($request->token)))) {
            return $gagal();
        }

        session(['pengajuan_siswa_id' => $siswa->id]);

        return redirect()->route('pengajuan-publik.form', $npsn);
    }

    private function siswaTerverifikasi(Sekolah $sekolah): ?Siswa
    {
        $siswaId = session('pengajuan_siswa_id');
        if (! $siswaId) return null;

        return Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('id', $siswaId)->first();
    }

    /** Halaman form isian (view data sekarang + tombol edit per field) */
    public function form(string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');

        $pengajuan = PengajuanPerubahan::buatAtauAmbilUntuk($siswa);

        return view('pengajuan-publik.form', compact('sekolah', 'siswa', 'pengajuan', 'npsn'));
    }

    public function simpan(Request $request, string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');

        $request->validate([
            'perubahan' => 'nullable|array',
            'catatan_siswa' => 'nullable|string|max:500',
        ]);

        $dataPerubahan = [];
        foreach ($request->input('perubahan', []) as $field => $nilai) {
            if (! in_array($field, PengajuanPerubahan::FIELD_BOLEH_DIAJUKAN)) continue;
            if (trim((string) $nilai) === '') continue;

            // tanggal_lahir itu Carbon object - (string) casting-nya bisa
            // beda format ("Y-m-d H:i:s") drpd input HTML date ("Y-m-d"),
            // jadi tanggal yg SAMA bisa keliru terdeteksi "beda". Samakan dulu.
            $nilaiInduk = $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('Y-m-d') : (string) $siswa->{$field};
            if ((string) $nilai === (string) $nilaiInduk) continue;

            $dataPerubahan[$field] = $nilai;
        }

        if (empty($dataPerubahan)) {
            return back()->with('error', 'Tidak ada perubahan yang diisi.');
        }

        $pengajuan = PengajuanPerubahan::buatAtauAmbilUntuk($siswa);
        $pengajuan->update([
            'status' => 'menunggu_approval',
            'data_perubahan' => $dataPerubahan,
            'catatan_siswa' => $request->catatan_siswa,
            'diajukan_at' => now(),
        ]);

        return redirect()->route('pengajuan-publik.form', $npsn)->with('success', 'Pengajuan perubahan berhasil dikirim. Menunggu persetujuan wali kelas.');
    }

    public function keluar(string $npsn)
    {
        session()->forget('pengajuan_siswa_id');
        return redirect()->route('pengajuan-publik.verifikasi', $npsn);
    }
}
