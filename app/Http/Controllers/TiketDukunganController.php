<?php

namespace App\Http\Controllers;

use App\Models\TiketDukungan;
use App\Models\TiketPesan;
use Illuminate\Http\Request;

class TiketDukunganController extends Controller
{
    public function index()
    {
        $tiketList = TiketDukungan::where('sekolah_id', auth()->user()->sekolah_id)
            ->with('dibuatOleh')
            ->orderByDesc('dibalas_terakhir_at')
            ->orderByDesc('created_at')
            ->get();

        return view('tiket.index', compact('tiketList'));
    }

    public function create()
    {
        return view('tiket.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subjek' => 'required|string|max:150',
            'pesan' => 'required|string|max:3000',
        ]);

        $tiket = TiketDukungan::create([
            'sekolah_id' => auth()->user()->sekolah_id,
            'dibuat_oleh_user_id' => auth()->id(),
            'subjek' => $data['subjek'],
            'status' => 'terbuka',
            'dibalas_terakhir_at' => now(),
            'ada_balasan_belum_dibaca_admin' => true,
        ]);

        TiketPesan::create([
            'tiket_id' => $tiket->id,
            'user_id' => auth()->id(),
            'dari_superadmin' => false,
            'pesan' => $data['pesan'],
        ]);

        return redirect()->route('tiket.show', $tiket)->with('success', 'Tiket berhasil dikirim. Admin akan segera membalas.');
    }

    public function show(TiketDukungan $tiket)
    {
        abort_unless($tiket->sekolah_id === auth()->user()->sekolah_id, 403);

        $tiket->load('pesan.user');
        $tiket->update(['ada_balasan_belum_dibaca_sekolah' => false]);

        return view('tiket.show', compact('tiket'));
    }

    public function balas(Request $request, TiketDukungan $tiket)
    {
        abort_unless($tiket->sekolah_id === auth()->user()->sekolah_id, 403);

        $data = $request->validate(['pesan' => 'required|string|max:3000']);

        TiketPesan::create([
            'tiket_id' => $tiket->id,
            'user_id' => auth()->id(),
            'dari_superadmin' => false,
            'pesan' => $data['pesan'],
        ]);

        $tiket->update([
            'dibalas_terakhir_at' => now(),
            'ada_balasan_belum_dibaca_admin' => true,
            'status' => $tiket->status === 'selesai' ? 'terbuka' : $tiket->status,
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
