<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NpsnLookupController extends Controller
{
    /**
     * Ambil data sekolah dari NPSN lewat data referensi resmi Kemendikdasmen.
     * Sumber ini publik (tidak perlu login/API key) di:
     * https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/{npsn}
     *
     * Halaman itu HTML biasa (bukan JSON API resmi), jadi di sini kita ambil
     * teksnya lalu parse baris "Label : Nilai" dengan regex - cukup stabil
     * karena situs itu memang menyajikan data dalam format daftar seperti itu.
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'npsn' => ['required', 'digits_between:6,10'],
        ]);

        $npsn = $request->input('npsn');

        try {
            $response = Http::timeout(10)->get("https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/{$npsn}");
        } catch (\Throwable $e) {
            return response()->json([
                'found' => false,
                'message' => 'Tidak dapat menghubungi server data referensi Kemendikdasmen. Coba lagi sebentar.',
            ], 503);
        }

        if (! $response->ok()) {
            return response()->json([
                'found' => false,
                'message' => 'NPSN tidak ditemukan atau server referensi sedang bermasalah.',
            ], 404);
        }

        $text = strip_tags($response->body());
        // Decode entity HTML (&nbsp; dkk) SEBELUM di-regex - kalau tidak,
        // &nbsp; ikut kebawa ke nilai field DAN bikin pola label gagal
        // cocok (krn spasi antar kata di label jadi "&nbsp;" bukan spasi biasa).
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        $extract = function (string $label) use ($text) {
            // Pola: "Label : nilai" berhenti di label berikutnya atau baris baru logis
            if (preg_match('/' . preg_quote($label, '/') . '\s*:\s*([^:]+?)(?=\s+[A-Z][a-zA-Z\.\-\/ ]{2,30}\s*:|$)/u', $text, $m)) {
                $val = trim($m[1]);
                // Jaga-jaga kalau masih ada &nbsp; literal yang lolos (mis. double-encoded)
                $val = str_ireplace('&nbsp;', ' ', $val);
                $val = preg_replace('/\s+/', ' ', $val);
                return trim($val) ?: null;
            }
            return null;
        };

        $nama = $extract('Nama');
        $foundNpsn = $extract('NPSN');

        if (! $nama || ! str_contains($text, $npsn)) {
            return response()->json([
                'found' => false,
                'message' => 'NPSN tidak ditemukan di data referensi Kemendikdasmen. Periksa kembali nomornya.',
            ], 404);
        }

        return response()->json([
            'found' => true,
            'data' => [
                'npsn' => $npsn,
                'nama' => $nama,
                'alamat' => $extract('Alamat'),
                'desa_kelurahan' => $extract('Desa/Kelurahan'),
                'kecamatan' => $extract('Kecamatan/Kota (LN)') ?? $extract('Kecamatan'),
                'kabupaten_kota' => $extract('Kab.-Kota/Negara (LN)') ?? $extract('Kabupaten/Kota'),
                'provinsi' => $extract('Propinsi/Luar Negeri (LN)') ?? $extract('Provinsi'),
                'status_sekolah' => $extract('Status Sekolah'),
                'bentuk_pendidikan' => $extract('Bentuk Pendidikan'),
                'jenjang_pendidikan' => $extract('Jenjang Pendidikan'),
            ],
        ]);
    }
}
