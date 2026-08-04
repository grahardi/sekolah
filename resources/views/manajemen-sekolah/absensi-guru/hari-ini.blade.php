@extends('layouts.manajemen-sekolah')
@section('title', 'Absensi Guru Hari Ini')

@php
    $warnaStatus = ['Sakit'=>'#fecaca','Izin'=>'#fde68a','Alpha'=>'#e2e8f0','Dispensasi'=>'#bfdbfe'];
    $teksStatus = ['Sakit'=>'#991b1b','Izin'=>'#92400e','Alpha'=>'#334155','Dispensasi'=>'#1e40af'];
@endphp

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Absensi Guru - Tidak Hadir Hari Ini</h2>
    <a href="{{ route('manajemen-sekolah.absensi-guru.index') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Isi Absensi Guru</a>
</div>

<form method="GET" style="margin-bottom:16px;max-width:260px;">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
</form>

<div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
    @foreach($warnaStatus as $status => $warna)
    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;color:#64748b;">
        <span style="width:12px;height:12px;border-radius:3px;background:{{ $warna }};display:inline-block;"></span> {{ $status }}
    </span>
    @endforeach
</div>

<div class="card" style="overflow:hidden;">
    @forelse($data as $d)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;background:{{ $warnaStatus[$d->status] ?? '#fff' }};{{ !$loop->last ? 'border-bottom:1px solid rgba(0,0,0,.06);' : '' }}">
        <div>
            <p style="font-weight:700;color:{{ $teksStatus[$d->status] ?? '#0f172a' }};margin:0;font-size:13px;">{{ $d->guru->nama ?? '-' }}</p>
            @if($d->keterangan)<p style="font-size:11px;color:{{ $teksStatus[$d->status] ?? '#64748b' }};opacity:.8;margin:2px 0 0;">{{ $d->keterangan }}</p>@endif
        </div>
        <span class="badge" style="background:#fff;color:{{ $teksStatus[$d->status] ?? '#0f172a' }};">{{ $d->status }}</span>
    </div>
    @empty
    <p style="text-align:center;color:#94a3b8;padding:30px;">Semua guru hadir pada tanggal ini (tidak ada catatan sakit/izin/alpha).</p>
    @endforelse
</div>
@endsection
