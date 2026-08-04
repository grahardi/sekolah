@extends('layouts.manajemen-sekolah')
@section('title', 'Absensi Siswa Hari Ini')

@php
    $warnaStatus = ['Sakit'=>'#fecaca','Izin'=>'#fde68a','Alpha'=>'#e2e8f0','Dispensasi'=>'#bfdbfe'];
    $teksStatus = ['Sakit'=>'#991b1b','Izin'=>'#92400e','Alpha'=>'#334155','Dispensasi'=>'#1e40af'];
@endphp

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Absensi Siswa - Tidak Hadir Hari Ini</h2>
    <a href="{{ route('manajemen-sekolah.absensi.rekap') }}" class="btn btn-secondary"><i class="ti ti-report"></i> Rekap Bulanan</a>
</div>

<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
    <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
        <option value="">-- Semua kelas --</option>
        @foreach($daftarKelas as $k)
        @php [$kl,$rb]=explode('|',$k); @endphp
        <option value="{{ $k }}" {{ $kelasRombelFilter === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
        @endforeach
    </select>
</form>

<div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
    @foreach($warnaStatus as $status => $warna)
    <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;color:#64748b;">
        <span style="width:12px;height:12px;border-radius:3px;background:{{ $warna }};display:inline-block;"></span> {{ $status }}
    </span>
    @endforeach
</div>

<div style="display:flex;flex-direction:column;gap:10px;">
    @forelse($data as $i => $d)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-radius:12px;background:{{ $warnaStatus[$d->status] ?? '#fff' }};box-shadow:0 1px 2px rgba(0,0,0,.03);">
        <div>
            <p style="font-weight:700;color:{{ $teksStatus[$d->status] ?? '#0f172a' }};margin:0;font-size:13px;">{{ $d->siswa->nama_lengkap ?? '-' }}</p>
            <p style="font-size:11px;color:{{ $teksStatus[$d->status] ?? '#64748b' }};opacity:.8;margin:2px 0 0;">{{ $d->siswa?->rombel ? "{$d->siswa->kelas}-{$d->siswa->rombel}" : $d->siswa?->kelas }} @if($d->keterangan) &middot; {{ $d->keterangan }} @endif</p>
        </div>
        <span class="badge" style="background:#fff;color:{{ $teksStatus[$d->status] ?? '#0f172a' }};">{{ $d->status }}</span>
    </div>
    @empty
    <p style="text-align:center;color:#94a3b8;padding:30px;">Semua siswa hadir pada tanggal ini (tidak ada catatan sakit/izin/alpha).</p>
    @endforelse
</div>
@endsection
