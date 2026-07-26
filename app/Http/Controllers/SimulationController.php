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
     * kalau guru ingin menambah/menonaktifkan modul lewat panel admin, lengkap
     * dengan kolom seperti mapel, jenjang kelas, dan status publikasi.
     */
    private const CATALOG = [
        'bandul' => [
            'title' => 'Bandul Sederhana',
            'subject' => 'Fisika · Getaran',
            'desc' => 'Ubah panjang tali dan sudut awal, amati periode ayunan berubah secara real-time.',
            'component' => 'Simulations/Pendulum',
        ],
        'gerak-peluru' => [
            'title' => 'Gerak Peluru',
            'subject' => 'Fisika · Kinematika',
            'desc' => 'Atur sudut dan kecepatan awal, lihat lintasan parabola dan jarak jangkau.',
            'component' => 'Simulations/ProjectileMotion',
        ],
        'rangkaian-listrik' => [
            'title' => 'Rangkaian Listrik Seri',
            'subject' => 'Fisika · Listrik',
            'desc' => 'Geser tegangan dan hambatan, lihat aliran elektron dan hitung arus lewat Hukum Ohm.',
            'component' => 'Simulations/CircuitBuilder',
        ],
    ];

    public function index(Request $request): Response
    {
        $simulations = collect(self::CATALOG)
            ->map(fn ($sim, $slug) => [
                'slug' => $slug,
                'title' => $sim['title'],
                'subject' => $sim['subject'],
                'desc' => $sim['desc'],
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

        return Inertia::render($sim['component'], [
            'title' => $sim['title'],
        ]);
    }
}
