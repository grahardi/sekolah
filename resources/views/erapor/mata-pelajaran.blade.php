@extends('layouts.erapor')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Manajemen Mata Pelajaran')

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:24px 28px;margin-bottom:20px;color:#fff;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 4px;">Manajemen Mata Pelajaran</h2>
            <p style="font-size:13px;opacity:.85;margin:0;">Kelola daftar mata pelajaran (master data) untuk sekolah.</p>
        </div>
        <button type="button" onclick="document.getElementById('form-tambah').style.display='block'" class="btn btn-sm" style="background:#FBBF24;color:#1E293B;font-weight:700;">
            <i class="ti ti-square-plus"></i> Tambah Mata Pelajaran
        </button>
    </div>
</div>

<form method="GET" style="margin-bottom:16px;max-width:400px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama mata pelajaran..." class="form-input" onchange="this.form.submit()">
</form>

<form id="form-tambah" action="{{ route('erapor.mata-pelajaran.store') }}" method="POST" class="card" style="padding:16px;margin-bottom:20px;display:none;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end;">
    @csrf
    <div><label class="form-label">Nama Mata Pelajaran</label><input name="nama" class="form-input" placeholder="mis. Matematika" required></div>
    <div><label class="form-label">Kelompok (opsional)</label><input name="kelompok" class="form-input" placeholder="Umum / Muatan Lokal"></div>
    <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
</form>

@php
    $palet = ['#2563EB', '#7C3AED', '#DB2777', '#EA580C', '#16A34A', '#0891B2', '#4F46E5', '#0D9488', '#C026D3', '#CA8A04'];
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
    @forelse($mapels as $i => $m)
    @php $warna = $palet[$i % count($palet)]; @endphp
    <div class="card" style="overflow:hidden;padding:0;">
        <div style="background:{{ $warna }};padding:16px 18px;color:#fff;">
            <p style="font-size:15px;font-weight:800;margin:0 0 2px;">{{ $m->nama }}</p>
            <p style="font-size:11px;opacity:.85;margin:0;">{{ $m->kelompok ?? 'Umum' }}</p>
        </div>
        <div style="padding:16px 18px;">
            <div style="display:flex;gap:24px;margin-bottom:12px;">
                <div>
                    <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">{{ $m->jumlah_guru }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">Guru Pengampu</p>
                </div>
                <div>
                    <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">{{ $m->jumlah_tp }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">Tujuan Pembelajaran</p>
                </div>
            </div>
            <p style="font-size:11px;color:#94a3b8;margin:0 0 4px;">Guru Pengampu:</p>
            @if($m->guru_pengampu->isEmpty())
            <p style="font-size:12px;color:#dc2626;margin:0 0 12px;">Belum ada guru</p>
            @else
            <ol style="font-size:12px;color:#374151;margin:0 0 12px;padding-left:16px;">
                @foreach($m->guru_pengampu->take(4) as $g)
                <li>{{ $g->nama }}</li>
                @endforeach
                @if($m->guru_pengampu->count() > 4)
                <li style="color:#94a3b8;">...{{ $m->guru_pengampu->count() - 4 }} guru lainnya</li>
                @endif
            </ol>
            @endif

            <a href="{{ route('erapor.tp.index', ['mapel_id' => $m->id]) }}" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-bottom:8px;">
                <i class="ti ti-target-arrow"></i> Kelola Tujuan Pembelajaran
            </a>
            <div style="display:flex;gap:6px;">
                <form action="{{ route('erapor.mata-pelajaran.destroy', $m) }}" method="POST" style="flex:1;" onsubmit="return confirm('Hapus mata pelajaran ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-trash"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:30px;">Belum ada mata pelajaran.</p>
    @endforelse
</div>
@endsection
