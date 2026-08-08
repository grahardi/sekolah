@extends('layouts.app')

@section('title', 'Dashboard Buku Induk')
@section('page-title', 'Buku Induk Siswa')

@section('content')

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="padding:18px 20px;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Total Siswa</p>
        <p style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;">{{ $totalSiswa }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Semua status</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #22c55e;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Aktif</p>
        <p style="font-size:28px;font-weight:800;color:#16a34a;line-height:1;">{{ $totalAktif }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa aktif</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #3b82f6;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Laki-laki</p>
        <p style="font-size:28px;font-weight:800;color:#1d4ed8;line-height:1;">{{ $totalLaki }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa putra</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #ec4899;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Perempuan</p>
        <p style="font-size:28px;font-weight:800;color:#be185d;line-height:1;">{{ $totalPerempu }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa putri</p>
    </div>
</div>

@if($statistikBerkas->isNotEmpty())
<div class="card" style="margin-bottom:24px;">
    <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-file-check" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Kelengkapan Berkas Siswa Aktif</span></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
            @foreach($statistikBerkas as $b)
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                    <span style="font-size:12.5px;font-weight:600;color:#374151;">{{ $b['label'] }}</span>
                    <span style="font-size:12px;color:#94a3b8;">{{ $b['sudah'] }}/{{ $b['total'] }}</span>
                </div>
                <div style="background:#f1f5f9;border-radius:6px;height:8px;overflow:hidden;">
                    <div style="background:{{ $b['persen'] >= 80 ? '#16a34a' : ($b['persen'] >= 40 ? '#d97706' : '#dc2626') }};height:100%;width:{{ $b['persen'] }}%;"></div>
                </div>
                <p style="font-size:11px;color:#94a3b8;margin:3px 0 0;">{{ $b['persen'] }}% lengkap</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Menu Cepat</p>
@php
    $shortcut = [
        ['label' => 'Semua Siswa', 'icon' => 'ti-users', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'href' => route('siswa.index')],
        ['label' => 'Tambah Siswa', 'icon' => 'ti-user-plus', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'href' => route('siswa.create')],
        ['label' => 'Cetak Massal', 'icon' => 'ti-printer', 'bg' => '#c7d2fe', 'warna' => '#4338ca', 'href' => route('siswa.cetak-massal.pilih')],
        ['label' => 'Import Dapodik', 'icon' => 'ti-file-import', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'href' => route('siswa.import.form')],
        ['label' => 'Export Data', 'icon' => 'ti-file-export', 'bg' => '#fde68a', 'warna' => '#a16207', 'href' => route('siswa.export.choice')],
        ['label' => 'Kenaikan Kelas', 'icon' => 'ti-arrow-up-circle', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'href' => route('kenaikan.index')],
        ['label' => 'Import Berkas', 'icon' => 'ti-folder-plus', 'bg' => '#fbcfe8', 'warna' => '#be185d', 'href' => route('siswa.import.berkas.form')],
    ];
@endphp
<div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:14px;">
    @foreach($shortcut as $s)
    <a href="{{ $s['href'] }}" style="text-decoration:none;border-radius:10px;padding:24px 16px;text-align:center;background:{{ $s['bg'] }};display:flex;flex-direction:column;align-items:center;gap:10px;">
        <span style="width:46px;height:46px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
            <i class="ti {{ $s['icon'] }}" style="font-size:20px;color:{{ $s['warna'] }};"></i>
        </span>
        <span style="font-size:13px;font-weight:700;color:{{ $s['warna'] }};">{{ $s['label'] }}</span>
    </a>
    @endforeach
</div>
@endsection
