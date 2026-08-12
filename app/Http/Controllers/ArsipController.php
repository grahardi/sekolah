<?php

namespace App\Http\Controllers;

use App\Models\{Siswa, ArsipBerkas};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ArsipController extends Controller
{
    public function show(Siswa $siswa)
    {
        $arsip = $siswa->arsipBerkas ?? new ArsipBerkas(['siswa_id' => $siswa->id]);
        return view('siswa.arsip', compact('siswa','arsip'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'foto'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'akta_lahir'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah_sd'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'transkrip_nilai'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sertifikat_tka' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan'        => 'nullable|string|max:500',
        ]);

        $arsip = $siswa->arsipBerkas ?? new ArsipBerkas(['siswa_id' => $siswa->id]);
        $fields = ['foto','kartu_keluarga','akta_lahir','ijazah_sd','ijazah','transkrip_nilai','sertifikat_tka'];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                if ($arsip->$field) Storage::disk('public')->delete($arsip->$field);
                $arsip->$field = $request->file($field)->store("arsip/{$siswa->id}", 'public');
            }
        }

        $arsip->catatan  = $request->catatan;
        $arsip->siswa_id = $siswa->id;
        $arsip->save();

        return back()->with('success','Arsip berkas berhasil disimpan.');
    }

    public function updateLabelBerkasLain(Request $request, Siswa $siswa)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'label' => 'nullable|string|max:150',
        ]);

        $arsip = $siswa->arsipBerkas;
        abort_unless($arsip, 404);

        $arsip->updateLabelBerkasLain((int) $request->index, $request->label ?? '');

        return back()->with('success', 'Keterangan berkas berhasil diperbarui.');
    }

    public function pindahkanBerkasLain(Request $request, Siswa $siswa)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'field_tujuan' => 'required|in:kartu_keluarga,akta_lahir,ijazah_sd,ijazah,transkrip_nilai,sertifikat_tka',
        ]);

        $arsip = $siswa->arsipBerkas;
        abort_unless($arsip, 404);

        // Kalau field tujuan sudah ada isinya, hapus file lama dulu spy gak nyampah
        if ($arsip->{$request->field_tujuan}) {
            Storage::disk('public')->delete($arsip->{$request->field_tujuan});
        }

        $arsip->pindahkanBerkasLain((int) $request->index, $request->field_tujuan);

        return back()->with('success', 'Berkas berhasil dipindahkan.');
    }

    public function hapusBerkas(Request $request, Siswa $siswa)
    {
        $request->validate([
            'field' => 'required|in:foto,kartu_keluarga,akta_lahir,ijazah_sd,ijazah,transkrip_nilai,sertifikat_tka'
        ]);

        $arsip = $siswa->arsipBerkas;
        if ($arsip && $arsip->{$request->field}) {
            Storage::disk('public')->delete($arsip->{$request->field});
            $arsip->update([$request->field => null]);
        }

        return back()->with('success','Berkas berhasil dihapus.');
    }

    // ── Import Berkas Massal (cocokkan nama file = NIS/NISN) ──────────────────

    public function showImportBerkas()
    {
        return view('siswa.import-berkas', ['jenisBerkas' => $this->jenisBerkasList()]);
    }

    private function jenisBerkasList(): array
    {
        return [
            'foto'            => 'Foto Siswa (jpg/png)',
            'kartu_keluarga'  => 'Kartu Keluarga (PDF)',
            'akta_lahir'      => 'Akta Lahir (PDF)',
            'ijazah_sd'       => 'Ijazah SD/MI (PDF)',
            'ijazah'          => 'Ijazah SMP (PDF)',
            'transkrip_nilai' => 'Transkrip Nilai (PDF)',
            'sertifikat_tka'  => 'Sertifikat TKA (PDF)',
        ];
    }

    public function importBerkas(Request $request)
    {
        $request->validate([
            'jenis'    => 'required|in:foto,kartu_keluarga,akta_lahir,ijazah_sd,ijazah,transkrip_nilai,sertifikat_tka',
            'files'    => 'required|array|min:1',
            'files.*'  => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $jenis = $request->input('jenis');
        $imported = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            // Nama file tanpa ekstensi = NIS atau NISN siswa, mis. "15700.jpg" -> "15700"
            $identifier = trim(pathinfo($originalName, PATHINFO_FILENAME));

            $siswa = Siswa::where('nisn', $identifier)->orWhere('nis', $identifier)->first();

            if (! $siswa) {
                $errors[] = "File \"{$originalName}\": tidak ada siswa dengan NIS/NISN \"{$identifier}\".";
                continue;
            }

            $path = $file->store("arsip/{$siswa->id}", 'public');

            if ($jenis === 'foto') {
                // Foto profil utama (dipakai di daftar, kartu, cetak) ada di
                // tabel siswas sendiri, bukan arsip_berkas - update keduanya
                // supaya konsisten di semua tampilan.
                if ($siswa->foto) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $siswa->update(['foto' => $path]);
            }

            $arsip = $siswa->arsipBerkas ?? new ArsipBerkas(['siswa_id' => $siswa->id]);
            if ($arsip->{$jenis}) {
                Storage::disk('public')->delete($arsip->{$jenis});
            }
            $arsip->{$jenis} = $path;
            $arsip->siswa_id = $siswa->id;
            $arsip->save();

            $imported++;
        }

        $errorToken = null;
        if (count($errors) > 0) {
            $errorToken = uniqid('berkaserr_', true);
            Cache::put("import_errors_{$errorToken}", $errors, now()->addHour());
        }

        $jenisLabel = $this->jenisBerkasList()[$jenis] ?? $jenis;
        $msg = "Berhasil mencocokkan {$imported} berkas ({$jenisLabel}).";
        if (count($errors) > 0) $msg .= " " . count($errors) . " file tidak cocok dengan siswa manapun.";

        return redirect()->route('siswa.import.form')
            ->with(count($errors) > 0 ? 'warning' : 'success', $msg)
            ->with('import_error_token', $errorToken)
            ->with('import_error_count', count($errors));
    }
}
