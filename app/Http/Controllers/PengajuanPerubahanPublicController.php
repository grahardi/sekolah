<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPerubahan;
use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PengajuanPerubahanPublicController extends Controller
{
    /** Halaman verifikasi (masukkan No. Induk + Tanggal Lahir + Token) */
    public function verifikasi(string $npsn, string $kodeAkses)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('kode_akses', $kodeAkses)->firstOrFail();

        // Kalau sesi ini sudah lolos verifikasi sebelumnya, langsung ke form
        if (session("pengajuan_terverifikasi_{$siswa->id}")) {
            return redirect()->route('pengajuan-publik.form', [$npsn, $kodeAkses]);
        }

        return view('pengajuan-publik.verifikasi', compact('sekolah', 'siswa', 'npsn', 'kodeAkses'));
    }

    public function prosesVerifikasi(Request $request, string $npsn, string $kodeAkses)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('kode_akses', $kodeAkses)->firstOrFail();

        $request->validate([
            'no_induk' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'token' => 'required|string',
        ]);

        $pengajuan = PengajuanPerubahan::buatAtauAmbilUntuk($siswa);

        $indukCocok = $request->no_induk === $siswa->nis || $request->no_induk === $siswa->nisn;
        $tanggalCocok = $siswa->tanggal_lahir && $siswa->tanggal_lahir->format('Y-m-d') === $request->tanggal_lahir;
        $tokenCocok = hash_equals($pengajuan->token, trim($request->token));

        if (! $indukCocok || ! $tanggalCocok || ! $tokenCocok) {
            return back()->withErrors(['error' => 'No. Induk, Tanggal Lahir, atau Token tidak cocok. Cek lagi data yang dimasukkan, atau hubungi wali kelas untuk token yang benar.'])->withInput();
        }

        session(["pengajuan_terverifikasi_{$siswa->id}" => true]);

        return redirect()->route('pengajuan-publik.form', [$npsn, $kodeAkses]);
    }

    /** Halaman form isian (view data sekarang + tombol edit per field) */
    public function form(string $npsn, string $kodeAkses)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('kode_akses', $kodeAkses)->firstOrFail();

        abort_unless(session("pengajuan_terverifikasi_{$siswa->id}"), 403, 'Silahkan verifikasi identitas dulu.');

        $pengajuan = PengajuanPerubahan::buatAtauAmbilUntuk($siswa);

        return view('pengajuan-publik.form', compact('sekolah', 'siswa', 'pengajuan', 'npsn', 'kodeAkses'));
    }

    public function simpan(Request $request, string $npsn, string $kodeAkses)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('kode_akses', $kodeAkses)->firstOrFail();

        abort_unless(session("pengajuan_terverifikasi_{$siswa->id}"), 403, 'Silahkan verifikasi identitas dulu.');

        $request->validate([
            'perubahan' => 'nullable|array',
            'catatan_siswa' => 'nullable|string|max:500',
        ]);

        $dataPerubahan = [];
        foreach ($request->input('perubahan', []) as $field => $nilai) {
            if (! in_array($field, PengajuanPerubahan::FIELD_BOLEH_DIAJUKAN)) continue;
            if (trim((string) $nilai) === '') continue;
            if ((string) $nilai === (string) $siswa->{$field}) continue; // sama persis, gak usah diajukan
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

        return redirect()->route('pengajuan-publik.form', [$npsn, $kodeAkses])->with('success', 'Pengajuan perubahan berhasil dikirim. Menunggu persetujuan wali kelas.');
    }
}
