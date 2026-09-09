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

<form id="form-tambah" action="{{ route('erapor.mata-pelajaran.store') }}" method="POST" class="card" style="padding:16px;margin-bottom:20px;display:none;">
    @csrf
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:12px;">
        <div><label class="form-label">Nama Mata Pelajaran</label><input name="nama" class="form-input" placeholder="mis. Matematika" required></div>
        <div><label class="form-label">Kelompok (opsional)</label><input name="kelompok" class="form-input" placeholder="Umum / Muatan Lokal"></div>
    </div>
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:10px;cursor:pointer;">
        <input type="checkbox" name="is_agama" value="1" onchange="document.getElementById('wrap-agama-tambah').style.display=this.checked?'block':'none'">
        Ini Mapel Agama (cuma tampil ke siswa dgn agama yg dipilih)
    </label>
    <div id="wrap-agama-tambah" style="display:none;margin-bottom:14px;padding:12px;background:#f8fafc;border-radius:8px;">
        <p class="form-label" style="margin-bottom:8px;">Berlaku untuk Agama (bisa pilih lebih dari satu, mis. kalau 1 guru rangkap ajar 2 agama)</p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            @foreach(['Islam','Kristen','Katholik','Hindu','Budha','Khonghucu','Kepercayaan kpd Tuhan YME'] as $ag)
            <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#475569;"><input type="checkbox" name="agama_untuk[]" value="{{ $ag }}"> {{ $ag }}</label>
            @endforeach
        </div>
    </div>
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:14px;cursor:pointer;">
        <input type="checkbox" name="is_non_formal" value="1">
        Mapel Non-Formal (mis. BK - tidak masuk penilaian/rapor apapun)
    </label>
    <button class="btn btn-primary"><i class="ti ti-plus"></i> Tambah</button>
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
            <p style="font-size:11px;opacity:.85;margin:0;">{{ $m->kelompok ?? 'Umum' }}{{ $m->is_agama ? ' · Agama: ' . implode(', ', $m->agama_untuk ?? []) : '' }}{{ $m->is_non_formal ? ' · Non-Formal' : '' }}</p>
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
                <button type="button" onclick="document.getElementById('modal-edit-{{ $m->id }}').style.display='flex'" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;"><i class="ti ti-pencil"></i> Edit</button>
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

@foreach($mapels as $m)
<div id="modal-edit-{{ $m->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:400px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Edit Mata Pelajaran</p>
            <button type="button" onclick="document.getElementById('modal-edit-{{ $m->id }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('erapor.mata-pelajaran.update', $m) }}" method="POST">
            @csrf @method('PUT')
            <label class="form-label">Nama Mata Pelajaran</label>
            <input type="text" name="nama" value="{{ $m->nama }}" class="form-input" required style="margin-bottom:12px;">
            <label class="form-label">Kelompok</label>
            <input type="text" name="kelompok" value="{{ $m->kelompok }}" class="form-input" placeholder="Umum / Muatan Lokal" style="margin-bottom:12px;">
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:10px;cursor:pointer;">
                <input type="checkbox" name="is_agama" value="1" {{ $m->is_agama ? 'checked' : '' }} onchange="document.getElementById('wrap-agama-edit-{{ $m->id }}').style.display=this.checked?'block':'none'">
                Ini Mapel Agama
            </label>
            <div id="wrap-agama-edit-{{ $m->id }}" style="display:{{ $m->is_agama ? 'block' : 'none' }};margin-bottom:16px;padding:12px;background:#f8fafc;border-radius:8px;">
                <p class="form-label" style="margin-bottom:8px;">Berlaku untuk Agama</p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    @foreach(['Islam','Kristen','Katholik','Hindu','Budha','Khonghucu','Kepercayaan kpd Tuhan YME'] as $ag)
                    <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#475569;">
                        <input type="checkbox" name="agama_untuk[]" value="{{ $ag }}" {{ in_array($ag, $m->agama_untuk ?? []) ? 'checked' : '' }}> {{ $ag }}
                    </label>
                    @endforeach
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:16px;cursor:pointer;">
                <input type="checkbox" name="is_non_formal" value="1" {{ $m->is_non_formal ? 'checked' : '' }}>
                Mapel Non-Formal (tidak masuk penilaian/rapor apapun)
            </label>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endforeach
@endsection
