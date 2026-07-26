<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SimulationController extends Controller
{
    /**
     * Katalog simulasi. Nantinya bisa dipindah ke tabel `simulations` di database
     * kalau guru ingin menambah/menonaktifkan modul lewat panel admin.
     *
     * `component` menunjuk ke halaman Inertia sebenarnya. Modul yang belum punya
     * simulasi custom diarahkan ke `Simulations/ComingSoon` (placeholder rapi),
     * sehingga bisa ditambah bertahap tanpa mengubah struktur katalog.
     */
    private const CATALOG = [
        // ---------------- FISIKA ----------------
        'bandul' => ['category' => 'Fisika', 'title' => 'Bandul Sederhana', 'subject' => 'Getaran & Gelombang', 'desc' => 'Ubah panjang tali dan sudut awal, amati periode ayunan berubah secara real-time.', 'component' => 'Simulations/Pendulum'],
        'gerak-peluru' => ['category' => 'Fisika', 'title' => 'Gerak Peluru', 'subject' => 'Kinematika', 'desc' => 'Atur sudut dan kecepatan awal, lihat lintasan parabola dan jarak jangkau.', 'component' => 'Simulations/ProjectileMotion'],
        'rangkaian-listrik' => ['category' => 'Fisika', 'title' => 'Rangkaian Listrik Seri', 'subject' => 'Listrik & Magnet', 'desc' => 'Geser tegangan dan hambatan, lihat aliran elektron dan hitung arus lewat Hukum Ohm.', 'component' => 'Simulations/CircuitBuilder'],
        'gerak-harmonik' => ['category' => 'Fisika', 'title' => 'Gerak Harmonik Sederhana', 'subject' => 'Getaran & Gelombang', 'desc' => 'Pegas dan massa yang berosilasi, amati hubungan simpangan, kecepatan, dan percepatan.', 'component' => 'Simulations/HarmonicMotion'],
        'hukum-newton' => ['category' => 'Fisika', 'title' => 'Hukum Newton & Gaya', 'subject' => 'Mekanika', 'desc' => 'Terapkan gaya pada benda, lihat bagaimana massa mempengaruhi percepatan.', 'component' => 'Simulations/NewtonsLaw'],
        'optik-lensa' => ['category' => 'Fisika', 'title' => 'Optik: Lensa & Cermin', 'subject' => 'Optik Geometri', 'desc' => 'Geser posisi benda, amati pembentukan bayangan pada lensa cembung/cekung.', 'component' => 'Simulations/OpticsLens'],
        'gelombang-bunyi' => ['category' => 'Fisika', 'title' => 'Gelombang Bunyi', 'subject' => 'Getaran & Gelombang', 'desc' => 'Ubah frekuensi dan amplitudo, dengar dan lihat bentuk gelombang bunyi.', 'component' => 'Simulations/SoundWave'],
        'termodinamika-gas' => ['category' => 'Fisika', 'title' => 'Termodinamika Gas Ideal', 'subject' => 'Termodinamika', 'desc' => 'Ubah suhu dan volume, amati pergerakan partikel gas dan tekanan yang dihasilkan.', 'component' => 'Simulations/IdealGas'],
        'medan-magnet' => ['category' => 'Fisika', 'title' => 'Medan Magnet', 'subject' => 'Listrik & Magnet', 'desc' => 'Visualisasikan garis medan magnet di sekitar kawat berarus dan magnet batang.', 'component' => 'Simulations/MagneticField'],
        'tegangan-permukaan' => ['category' => 'Fisika', 'title' => 'Tegangan Permukaan & Fluida', 'subject' => 'Mekanika Fluida', 'desc' => 'Amati efek tegangan permukaan dan tekanan hidrostatis pada fluida.', 'component' => 'Simulations/SurfaceTension'],

        // ---------------- MATEMATIKA ----------------
        'fungsi-kuadrat' => ['category' => 'Matematika', 'title' => 'Fungsi Kuadrat Interaktif', 'subject' => 'Aljabar', 'desc' => 'Geser koefisien a, b, c dan lihat perubahan bentuk parabola secara langsung.'],
        'trigonometri-lingkaran' => ['category' => 'Matematika', 'title' => 'Trigonometri Lingkaran Satuan', 'subject' => 'Trigonometri', 'desc' => 'Putar sudut pada lingkaran satuan, amati nilai sin, cos, dan tan berubah.'],
        'transformasi-geometri' => ['category' => 'Matematika', 'title' => 'Transformasi Geometri', 'subject' => 'Geometri', 'desc' => 'Coba translasi, rotasi, refleksi, dan dilatasi pada bangun datar.'],
        'vektor-2d' => ['category' => 'Matematika', 'title' => 'Vektor 2D', 'subject' => 'Aljabar Linear', 'desc' => 'Jumlahkan dan kurangkan vektor secara visual, pahami komponen x-y.'],
        'turunan-garis-singgung' => ['category' => 'Matematika', 'title' => 'Turunan & Garis Singgung', 'subject' => 'Kalkulus', 'desc' => 'Geser titik pada kurva, lihat kemiringan garis singgung berubah real-time.'],
        'integral-luas' => ['category' => 'Matematika', 'title' => 'Integral sebagai Luas Daerah', 'subject' => 'Kalkulus', 'desc' => 'Visualisasikan integral tentu sebagai luas daerah di bawah kurva.'],
        'barisan-deret' => ['category' => 'Matematika', 'title' => 'Barisan & Deret', 'subject' => 'Aljabar', 'desc' => 'Eksplorasi pola barisan aritmetika dan geometri secara visual.'],
        'peluang-statistika' => ['category' => 'Matematika', 'title' => 'Peluang & Statistika', 'subject' => 'Statistika', 'desc' => 'Simulasi lempar dadu/koin berulang, amati distribusi hasil mendekati teori.'],
        'bangun-ruang-3d' => ['category' => 'Matematika', 'title' => 'Bangun Ruang 3D', 'subject' => 'Geometri Ruang', 'desc' => 'Putar dan potong bangun ruang, hitung volume serta luas permukaan.'],
        'persamaan-linear-dua-variabel' => ['category' => 'Matematika', 'title' => 'Persamaan Linear Dua Variabel', 'subject' => 'Aljabar', 'desc' => 'Geser dua garis, temukan titik potong sebagai solusi sistem persamaan.'],

        // ---------------- BIOLOGI ----------------
        'struktur-sel' => ['category' => 'Biologi', 'title' => 'Struktur Sel Hewan & Tumbuhan', 'subject' => 'Biologi Sel', 'desc' => 'Jelajahi organel sel secara interaktif, bandingkan sel hewan dan tumbuhan.'],
        'peredaran-darah' => ['category' => 'Biologi', 'title' => 'Sistem Peredaran Darah', 'subject' => 'Anatomi Manusia', 'desc' => 'Ikuti aliran darah melalui jantung, arteri, dan vena secara animasi.'],
        'fotosintesis' => ['category' => 'Biologi', 'title' => 'Fotosintesis', 'subject' => 'Fisiologi Tumbuhan', 'desc' => 'Atur intensitas cahaya dan CO2, amati laju fotosintesis berubah.'],
        'sistem-pencernaan' => ['category' => 'Biologi', 'title' => 'Sistem Pencernaan Manusia', 'subject' => 'Anatomi Manusia', 'desc' => 'Telusuri jalur makanan dari mulut hingga usus, pelajari fungsi tiap organ.'],
        'genetika-mendel' => ['category' => 'Biologi', 'title' => 'Genetika Persilangan (Hukum Mendel)', 'subject' => 'Genetika', 'desc' => 'Silangkan sifat induk, lihat rasio genotipe dan fenotipe keturunan.'],
        'ekosistem-rantai-makanan' => ['category' => 'Biologi', 'title' => 'Ekosistem & Rantai Makanan', 'subject' => 'Ekologi', 'desc' => 'Susun rantai makanan, amati dampak perubahan populasi salah satu spesies.'],
        'sistem-pernapasan' => ['category' => 'Biologi', 'title' => 'Sistem Pernapasan', 'subject' => 'Anatomi Manusia', 'desc' => 'Amati mekanisme inspirasi dan ekspirasi pada paru-paru manusia.'],
        'struktur-dna' => ['category' => 'Biologi', 'title' => 'Struktur DNA', 'subject' => 'Genetika', 'desc' => 'Jelajahi struktur heliks ganda DNA dan pasangan basa nitrogen.'],
        'siklus-air' => ['category' => 'Biologi', 'title' => 'Siklus Air', 'subject' => 'Ekologi', 'desc' => 'Ikuti tahap evaporasi, kondensasi, dan presipitasi dalam siklus air.'],
        'evolusi-seleksi-alam' => ['category' => 'Biologi', 'title' => 'Evolusi & Seleksi Alam', 'subject' => 'Evolusi', 'desc' => 'Simulasikan seleksi alam pada populasi dengan variasi sifat tertentu.'],
    ];

    public function index(Request $request): Response
    {
        $simulations = collect(self::CATALOG)
            ->map(fn ($sim, $slug) => [
                'slug' => $slug,
                'title' => $sim['title'],
                'category' => $sim['category'],
                'subject' => $sim['subject'],
                'desc' => $sim['desc'],
                'status' => isset($sim['component']) ? 'aktif' : 'segera',
            ])
            ->values();

        return Inertia::render('Simulations/Index', [
            'simulations' => $simulations,
        ]);
    }

    public function show(Request $request, string $slug): Response|HttpResponse
    {
        $sim = self::CATALOG[$slug] ?? null;

        abort_unless($sim, 404);

        $component = $sim['component'] ?? 'Simulations/ComingSoon';

        return Inertia::render($component, [
            'title' => $sim['title'],
            'category' => $sim['category'],
            'subject' => $sim['subject'],
        ]);
    }
}
