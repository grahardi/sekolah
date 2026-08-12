<?php

namespace App\Services;

use App\Models\Sekolah;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiOcrService
{
    // CATATAN: nama model ini perlu DICEK ULANG saat implementasi - daftar
    // model Gemini berubah cukup sering. Ganti lewat .env kalau perlu tanpa ubah kode.
    private string $model;

    public function __construct(private Sekolah $sekolah)
    {
        $this->model = env('GEMINI_MODEL', 'gemini-3.5-flash-lite');
    }

    private function apiKey(): ?string
    {
        return $this->sekolah->geminiApiKeyEfektif();
    }

    /** Tes cepat: cuma kirim teks sederhana, gak perlu PDF - buat verifikasi API key & model valid */
    public function tesKoneksi(): array
    {
        $apiKey = $this->apiKey();
        if (! $apiKey) {
            return ['ok' => false, 'pesan' => 'API key belum diatur (baik default sekolah.co.id maupun milik sekolah).'];
        }

        try {
            $response = Http::timeout(20)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}",
                [
                    'contents' => [['parts' => [['text' => 'Balas dengan kata "OK" saja.']]]],
                ]
            );

            if ($response->failed()) {
                return ['ok' => false, 'pesan' => "Gagal (HTTP {$response->status()}): " . $response->body()];
            }

            $teks = $response->json('candidates.0.content.parts.0.text');
            if (! $teks) {
                return ['ok' => false, 'pesan' => 'Respons kosong/format tidak sesuai. Cek nama model (GEMINI_MODEL di .env).'];
            }

            return ['ok' => true, 'pesan' => "Koneksi berhasil! Model '{$this->model}' merespons dengan benar."];
        } catch (\Throwable $e) {
            return ['ok' => false, 'pesan' => 'Error: ' . $e->getMessage()];
        }
    }

    /** Kirim 1 file PDF ke Gemini, minta hasil JSON sesuai prompt */
    private function panggilGemini(string $pathPdfRelatif, string $prompt): array
    {
        $apiKey = $this->apiKey();
        if (! $apiKey) {
            throw new \Exception('API key Gemini belum diatur (baik default sekolah.co.id maupun milik sekolah).');
        }

        if (! Storage::disk('public')->exists($pathPdfRelatif)) {
            throw new \Exception("File tidak ditemukan: {$pathPdfRelatif}");
        }

        $base64 = base64_encode(Storage::disk('public')->get($pathPdfRelatif));

        $response = Http::timeout(60)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}",
            [
                'contents' => [[
                    'parts' => [
                        ['inline_data' => ['mime_type' => 'application/pdf', 'data' => $base64]],
                        ['text' => $prompt],
                    ],
                ]],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Gemini API error: ' . $response->status() . ' - ' . $response->body());
        }

        $teks = $response->json('candidates.0.content.parts.0.text');
        if (! $teks) {
            throw new \Exception('Respons Gemini kosong/tidak sesuai format.');
        }

        $hasil = json_decode($teks, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Gagal parse JSON dari Gemini: ' . json_last_error_msg());
        }

        return $hasil;
    }

    public function scanKk(string $pathPdf, string $namaSiswa): array
    {
        $prompt = <<<PROMPT
        Kamu adalah sistem OCR Kartu Keluarga (KK) Indonesia. Dokumen KK punya 2 bagian tabel:
        1. TABEL ATAS (Daftar Anggota Keluarga): kolom No., Nama Lengkap, NIK, dll - tiap orang punya nomor urut.
        2. TABEL BAWAH (biasanya judulnya "NAMA ORANG TUA" / mengandung kolom "Nama Ayah" dan "Nama Ibu"): berisi nama ayah & ibu KANDUNG untuk TIAP orang, dicocokkan lewat NOMOR URUT yang SAMA dengan tabel atas (bukan nama).

        LANGKAH WAJIB (JANGAN DILEWATI):
        Langkah 1: Di TABEL ATAS, cari baris dengan nama paling mirip dengan "{$namaSiswa}". Catat NOMOR URUT (No.) baris itu.
        Langkah 2: Di TABEL BAWAH, cari baris dengan NOMOR URUT YANG SAMA PERSIS dari Langkah 1. Ambil isi kolom "Nama Ayah" dan "Nama Ibu" DARI BARIS ITU SAJA.

        KESALAHAN YANG SERING TERJADI (HINDARI): JANGAN asal ambil nama ayah/ibu dari nomor urut 1 (Kepala Keluarga) atau dari baris pertama yang terlihat. Nama ayah/ibu WAJIB diambil dari baris dengan nomor urut yang SAMA PERSIS dengan nomor urut siswa "{$namaSiswa}" di tabel atas, meskipun siswa itu bukan orang pertama dalam KK.

        Ekstrak juga data berikut ke JSON:
        - no_kk, alamat, rt, rw, kode_pos, desa_kelurahan, kecamatan, kabupaten_kota, provinsi
        - anak_ke: perkiraan siswa ini anak ke berapa dalam keluarga (hitung dari urutan anak² yg statusnya "Anak" dlm KK, diurutkan dari yg tertua berdasarkan tanggal lahir). Kalau gak yakin/gak bisa dipastikan, isi null.

        ATURAN PENTING: Pada blok 'anggota_keluarga', HANYA masukkan maksimal 3 orang:
        1. Siswa (namanya paling mirip "{$namaSiswa}"), 2. Ayah Siswa (hasil Langkah 2), 3. Ibu Siswa (hasil Langkah 2). Abaikan Kakek/Nenek/Paman/anggota keluarga lain.
        Tiap anggota cukup: nik, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, pendidikan, jenis_pekerjaan.

        FORMAT JSON WAJIB:
        {
          "skor_kejelasan": 100,
          "no_kk": "", "alamat": "", "rt": "", "rw": "", "kode_pos": "",
          "desa_kelurahan": "", "kecamatan": "", "kabupaten_kota": "", "provinsi": "",
          "nomor_urut_siswa_di_tabel_atas": null,
          "nama_ayah_siswa": "", "nama_ibu_siswa": "", "anak_ke": null,
          "anggota_keluarga": [{"nama_lengkap": "", "nik": "", "jenis_kelamin": "", "tempat_lahir": "", "tanggal_lahir": "", "agama": "", "pendidikan": "", "jenis_pekerjaan": ""}]
        }
        PROMPT;

        return $this->panggilGemini($pathPdf, $prompt);
    }

    public function scanAkta(string $pathPdf): array
    {
        $prompt = <<<PROMPT
        Kamu adalah sistem OCR khusus Akta Kelahiran Indonesia.
        Tugas utamamu adalah mengekstrak 'Nomor Registrasi Akta Kelahiran'.

        ATURAN PENTING:
        1. JANGAN ambil nomor seri cetakan di pojok kanan/kiri atas (contoh SALAH: AL. 705.xxx).
        2. JANGAN ambil NIK.
        3. Nomor Registrasi yang BENAR ada di TENGAH dokumen, setelah kalimat "Berdasarkan Akta Kelahiran Nomor" atau "By virtue of Birth Certificate Number".
        4. Contoh format benar: 3507-LT-02022015-0190 atau serupa.

        FORMAT JSON WAJIB:
        {"skor_kejelasan_akta": 100, "nomor_registrasi_akta": "string"}
        PROMPT;

        return $this->panggilGemini($pathPdf, $prompt);
    }
}
