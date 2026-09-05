<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KontenHalaman;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KontenHalamanController extends Controller
{
    private const KUNCI_KONTEN = [
        'hero_tag' => 'Hero - Tag Kecil di Atas Judul',
        'hero_title' => 'Hero - Judul Utama',
        'hero_desc' => 'Hero - Deskripsi',
        'stat_1_value' => 'Statistik 1 - Angka',
        'stat_1_label' => 'Statistik 1 - Label',
        'stat_2_value' => 'Statistik 2 - Angka',
        'stat_2_label' => 'Statistik 2 - Label',
        'stat_3_value' => 'Statistik 3 - Angka',
        'stat_3_label' => 'Statistik 3 - Label',
        'stat_4_value' => 'Statistik 4 - Angka',
        'stat_4_label' => 'Statistik 4 - Label',
        'fitur_1_title' => 'Fitur 1 - Judul',
        'fitur_1_desc' => 'Fitur 1 - Deskripsi',
        'fitur_2_title' => 'Fitur 2 - Judul',
        'fitur_2_desc' => 'Fitur 2 - Deskripsi',
        'fitur_3_title' => 'Fitur 3 - Judul',
        'fitur_3_desc' => 'Fitur 3 - Deskripsi',
        'fitur_4_title' => 'Fitur 4 - Judul',
        'fitur_4_desc' => 'Fitur 4 - Deskripsi',
    ];

    public function index()
    {
        $nilaiTersimpan = KontenHalaman::ambilSemua('welcome');

        $fields = collect(self::KUNCI_KONTEN)->map(fn ($label, $kunci) => [
            'kunci' => $kunci,
            'label' => $label,
            'nilai' => $nilaiTersimpan[$kunci] ?? '',
        ])->values();

        return Inertia::render('SuperAdmin/KontenHalamanIndex', ['fields' => $fields]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['konten' => 'required|array']);

        $kunciValid = array_keys(self::KUNCI_KONTEN);
        $filtered = array_intersect_key($data['konten'], array_flip($kunciValid));

        KontenHalaman::simpan('welcome', $filtered);

        return back()->with('success', 'Konten halaman depan berhasil disimpan.');
    }
}
