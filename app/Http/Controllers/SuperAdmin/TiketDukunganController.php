<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TiketDukungan;
use App\Models\TiketPesan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TiketDukunganController extends Controller
{
    public function index(Request $request)
    {
        $filterStatus = $request->input('status');

        $tiketList = TiketDukungan::with('sekolah', 'dibuatOleh')
            ->when($filterStatus, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('ada_balasan_belum_dibaca_admin')
            ->orderByDesc('dibalas_terakhir_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'subjek' => $t->subjek,
                'status' => $t->status,
                'label_status' => $t->labelStatus(),
                'sekolah_nama' => $t->sekolah->nama ?? '-',
                'dibuat_oleh' => $t->dibuatOleh->name ?? '-',
                'dibalas_terakhir_at' => $t->dibalas_terakhir_at?->diffForHumans(),
                'ada_balasan_belum_dibaca_admin' => $t->ada_balasan_belum_dibaca_admin,
            ]);

        $jumlahBelumDibaca = TiketDukungan::where('ada_balasan_belum_dibaca_admin', true)->count();

        return Inertia::render('SuperAdmin/TiketIndex', [
            'tiketList' => $tiketList,
            'filterStatus' => $filterStatus,
            'jumlahBelumDibaca' => $jumlahBelumDibaca,
        ]);
    }

    public function show(TiketDukungan $tiket)
    {
        $tiket->load('pesan.user', 'sekolah', 'dibuatOleh');
        $tiket->update(['ada_balasan_belum_dibaca_admin' => false]);

        return Inertia::render('SuperAdmin/TiketShow', [
            'tiket' => [
                'id' => $tiket->id,
                'subjek' => $tiket->subjek,
                'status' => $tiket->status,
                'label_status' => $tiket->labelStatus(),
                'sekolah_nama' => $tiket->sekolah->nama ?? '-',
                'dibuat_oleh' => $tiket->dibuatOleh->name ?? '-',
                'created_at' => $tiket->created_at->diffForHumans(),
                'pesan' => $tiket->pesan->map(fn ($p) => [
                    'id' => $p->id,
                    'pesan' => $p->pesan,
                    'dari_superadmin' => $p->dari_superadmin,
                    'nama_pengirim' => $p->dari_superadmin ? 'Admin sekolah.co.id' : ($p->user->name ?? '-'),
                    'waktu' => $p->created_at->format('d M Y, H:i'),
                ]),
            ],
        ]);
    }

    public function balas(Request $request, TiketDukungan $tiket)
    {
        $data = $request->validate([
            'pesan' => 'required|string|max:3000',
            'status' => 'required|in:terbuka,diproses,selesai',
        ]);

        TiketPesan::create([
            'tiket_id' => $tiket->id,
            'user_id' => null,
            'dari_superadmin' => true,
            'pesan' => $data['pesan'],
        ]);

        $tiket->update([
            'status' => $data['status'],
            'dibalas_terakhir_at' => now(),
            'ada_balasan_belum_dibaca_sekolah' => true,
        ]);

        return back()->with('success', 'Balasan terkirim ke sekolah.');
    }
}
