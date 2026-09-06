<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\SekolahTujuan;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AlumniPublicController extends Controller
{
    public function verifikasi(string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();

        if (session('alumni_publik_siswa_id')) {
            return redirect()->route('alumni-publik.form', $npsn);
        }

        return view('alumni-publik.verifikasi', compact('sekolah', 'npsn'));
    }

    public function prosesVerifikasi(Request $request, string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();

        $request->validate([
            'no_induk' => 'required|string',
            'tanggal_lahir' => 'required|date',
        ]);

        $siswa = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)
            ->where('status', 'lulus')
            ->where(function ($q) use ($request) {
                $q->where('nis', $request->no_induk)->orWhere('nisn', $request->no_induk);
            })
            ->first();

        $gagal = fn () => back()->withErrors(['error' => 'No. Induk atau Tanggal Lahir tidak cocok dengan data alumni kami. Cek lagi data yang dimasukkan.'])->withInput();

        if (! $siswa) return $gagal();

        $tanggalCocok = $siswa->tanggal_lahir && $siswa->tanggal_lahir->format('Y-m-d') === $request->tanggal_lahir;
        if (! $tanggalCocok) return $gagal();

        session(['alumni_publik_siswa_id' => $siswa->id]);

        return redirect()->route('alumni-publik.form', $npsn);
    }

    private function siswaTerverifikasi(Sekolah $sekolah): ?Siswa
    {
        $siswaId = session('alumni_publik_siswa_id');
        if (! $siswaId) return null;

        return Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('id', $siswaId)->where('status', 'lulus')->first();
    }

    public function form(string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');

        $sekolahTujuanList = SekolahTujuan::where('sekolah_id', $sekolah->id)->where('aktif', true)->orderBy('urutan')->orderBy('nama_sekolah')->get();

        // Kalau sudah pernah isi, tampilkan hasil isian (LOCKED) - gak bisa
        // edit langsung lagi. Kalau ada Ajuan Ulang yg lagi nunggu approval,
        // tampilkan jg statusnya.
        if ($siswa->alumni_diisi_at) {
            $ajuanUlangAktif = \App\Models\AlumniAjuanUlang::where('siswa_id', $siswa->id)->where('status', 'menunggu')->latest()->first();

            return view('alumni-publik.locked', compact('sekolah', 'siswa', 'sekolahTujuanList', 'npsn', 'ajuanUlangAktif'));
        }

        return view('alumni-publik.form', compact('sekolah', 'siswa', 'sekolahTujuanList', 'npsn'));
    }

    public function simpan(Request $request, string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');

        // Cuma bisa isi via jalur ini kalau BELUM PERNAH isi sama sekali.
        // Kalau sudah pernah, harus lewat Ajuan Ulang (perlu approval admin).
        abort_if($siswa->alumni_diisi_at, 403, 'Data sudah pernah diisi. Gunakan fitur Ajukan Perubahan.');

        $data = $request->validate([
            'alumni_kategori' => 'required|in:lanjut_sekolah,pondok_pesantren,tidak_melanjutkan,bekerja,lainnya',
            'alumni_sekolah_tujuan_id' => 'nullable|exists:sekolah_tujuan,id',
            'alumni_sekolah_tujuan_manual' => 'nullable|string|max:150',
            'alumni_jurusan' => 'nullable|string|max:100',
            'alumni_keterangan' => 'nullable|string|max:255',
        ]);

        $siswa->update([
            'alumni_kategori' => $data['alumni_kategori'],
            'alumni_sekolah_tujuan_id' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_sekolah_tujuan_id'] ?? null) : null,
            'alumni_sekolah_tujuan_manual' => in_array($data['alumni_kategori'], ['lanjut_sekolah', 'pondok_pesantren']) ? ($data['alumni_sekolah_tujuan_manual'] ?? null) : null,
            'alumni_jurusan' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_jurusan'] ?? null) : null,
            'alumni_keterangan' => in_array($data['alumni_kategori'], ['bekerja', 'tidak_melanjutkan', 'lainnya']) ? ($data['alumni_keterangan'] ?? null) : null,
            'alumni_diisi_at' => now(),
        ]);

        return redirect()->route('alumni-publik.form', $npsn)->with('success', 'Terima kasih! Data kamu berhasil disimpan.');
    }

    /** Halaman form Ajuan Ulang (dipakai kalau mau UBAH data yg udah locked) */
    public function ajukanUlangForm(string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');
        abort_unless($siswa->alumni_diisi_at, 404);

        $sekolahTujuanList = SekolahTujuan::where('sekolah_id', $sekolah->id)->where('aktif', true)->orderBy('urutan')->orderBy('nama_sekolah')->get();

        return view('alumni-publik.ajuan-ulang', compact('sekolah', 'siswa', 'sekolahTujuanList', 'npsn'));
    }

    public function ajukanUlangSimpan(Request $request, string $npsn)
    {
        $sekolah = Sekolah::where('npsn', $npsn)->firstOrFail();
        $siswa = $this->siswaTerverifikasi($sekolah);
        abort_unless($siswa, 403, 'Silahkan verifikasi identitas dulu.');

        $data = $request->validate([
            'alumni_kategori' => 'required|in:lanjut_sekolah,pondok_pesantren,tidak_melanjutkan,bekerja,lainnya',
            'alumni_sekolah_tujuan_id' => 'nullable|exists:sekolah_tujuan,id',
            'alumni_sekolah_tujuan_manual' => 'nullable|string|max:150',
            'alumni_jurusan' => 'nullable|string|max:100',
            'alumni_keterangan' => 'nullable|string|max:255',
        ]);

        // Kalau masih ada ajuan lama yg belum diproses, timpa aja (drpd numpuk)
        \App\Models\AlumniAjuanUlang::where('siswa_id', $siswa->id)->where('status', 'menunggu')->delete();

        \App\Models\AlumniAjuanUlang::create([
            'siswa_id' => $siswa->id,
            'alumni_kategori' => $data['alumni_kategori'],
            'alumni_sekolah_tujuan_id' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_sekolah_tujuan_id'] ?? null) : null,
            'alumni_sekolah_tujuan_manual' => in_array($data['alumni_kategori'], ['lanjut_sekolah', 'pondok_pesantren']) ? ($data['alumni_sekolah_tujuan_manual'] ?? null) : null,
            'alumni_jurusan' => $data['alumni_kategori'] === 'lanjut_sekolah' ? ($data['alumni_jurusan'] ?? null) : null,
            'alumni_keterangan' => in_array($data['alumni_kategori'], ['bekerja', 'tidak_melanjutkan', 'lainnya']) ? ($data['alumni_keterangan'] ?? null) : null,
            'status' => 'menunggu',
        ]);

        return redirect()->route('alumni-publik.form', $npsn)->with('success', 'Ajuan perubahan berhasil dikirim, menunggu persetujuan admin sekolah.');
    }

    public function keluar(string $npsn)
    {
        session()->forget('alumni_publik_siswa_id');
        return redirect()->route('alumni-publik.verifikasi', $npsn);
    }
}
