@extends('layouts.manajemen-sekolah')
@section('title', 'Menu Piket')
@section('page-title', 'Menu Piket')

@section('content')
@php
    $menu = [
        ['label' => 'Isi Absensi', 'icon' => 'ti-pencil', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.index')],
        ['label' => 'Isi Keterlambatan', 'icon' => 'ti-clock', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ['label' => 'Siswa Terlambat', 'icon' => 'ti-alarm', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ['label' => 'Absensi Siswa', 'icon' => 'ti-checkbox', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.rekap')],
        ['label' => 'Arsip Surat', 'icon' => 'ti-briefcase', 'bg' => '#fffbeb', 'warna' => '#d97706', 'segera' => true],
        ['label' => 'Ajuan Absensi Masuk', 'icon' => 'ti-message-report', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ['label' => 'Ajuan Piket Guru', 'icon' => 'ti-user-exclamation', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ['label' => 'Ajuan WhatsApp', 'icon' => 'ti-brand-whatsapp', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
        ['label' => 'Absensi Guru', 'icon' => 'ti-users', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
        ['label' => 'Tugas Guru Absen', 'icon' => 'ti-clipboard-text', 'bg' => '#fce7f3', 'warna' => '#db2777', 'segera' => true],
    ];
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
    @foreach($menu as $m)
    @if(!empty($m['segera']))
    <div class="sb-item-demo" style="border-radius:14px;padding:22px 16px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:10px;">
        <span style="width:44px;height:44px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
            <i class="ti {{ $m['icon'] }}" style="font-size:20px;color:{{ $m['warna'] }};"></i>
        </span>
        <span style="font-size:13px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
        <span class="sb-demo-badge">Segera</span>
    </div>
    @else
    <a href="{{ $m['href'] }}" style="text-decoration:none;border-radius:14px;padding:22px 16px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:10px;transition:transform .1s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
        <span style="width:44px;height:44px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
            <i class="ti {{ $m['icon'] }}" style="font-size:20px;color:{{ $m['warna'] }};"></i>
        </span>
        <span style="font-size:13px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
    </a>
    @endif
    @endforeach
</div>
@endsection
