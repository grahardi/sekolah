@extends('layouts.manajemen-sekolah')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Manajemen Sekolah')

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:#eff6ff;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#1e40af;margin:0;">{{ $totalSiswaAktif }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Total Siswa Aktif</p>
    </div>
    <div style="background:#f0fdf4;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#16a34a;margin:0;">{{ $totalGuru }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Total Guru</p>
    </div>
    <div style="background:#fffbeb;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#d97706;margin:0;">{{ $sudahDiabsenHariIni }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Sudah Diabsen Hari Ini</p>
    </div>
</div>

<div class="card" style="padding:20px;margin-bottom:20px;">
    <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">Rekap Absensi Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }})</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;">
        @foreach(['Hadir'=>'#16a34a','Sakit'=>'#dc2626','Izin'=>'#d97706','Alpha'=>'#64748b','Dispensasi'=>'#2563EB'] as $status => $warna)
        <div style="text-align:center;padding:12px;background:#f8fafc;border-radius:10px;">
            <p style="font-size:22px;font-weight:800;color:{{ $warna }};margin:0;">{{ $rekapHariIni[$status] ?? 0 }}</p>
            <p style="font-size:11px;color:#64748b;margin:2px 0 0;">{{ $status }}</p>
        </div>
        @endforeach
    </div>
</div>

<div class="card" style="padding:20px;">
    <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">Aksi Cepat</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('manajemen-sekolah.absensi.index') }}" class="btn btn-primary"><i class="ti ti-calendar-check"></i> Input Absensi Hari Ini</a>
        <a href="{{ route('manajemen-sekolah.data-siswa') }}" class="btn btn-secondary"><i class="ti ti-users"></i> Lihat Data Siswa</a>
        <a href="{{ route('manajemen-sekolah.data-guru') }}" class="btn btn-secondary"><i class="ti ti-user-check"></i> Lihat Data Guru</a>
    </div>
</div>
@endsection
