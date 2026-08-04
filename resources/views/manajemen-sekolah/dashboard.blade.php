@extends('layouts.manajemen-sekolah')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Manajemen Sekolah')

@section('content')
@php
    $user = auth()->user();
    $guruSaya = $user->isAdmin() ? null : \App\Models\Guru::where('user_id', $user->id)->first();

    $daftarPeran = [];
    if ($user->isAdmin()) $daftarPeran[] = 'admin';
    if ($guruSaya) {
        $daftarPeran[] = 'guru';
        foreach (['is_piket'=>'piket','is_tatib'=>'tatib','is_bk'=>'bk','is_kebersihan'=>'kebersihan','is_keagamaan'=>'keagamaan','is_kepsek'=>'kepsek','is_kesiswaan'=>'kesiswaan'] as $flag=>$label) {
            if ($guruSaya->{$flag}) $daftarPeran[] = $label;
        }
    }

    $bisaPiket = $user->isAdmin() || ($guruSaya && $guruSaya->is_piket);
@endphp

<div class="card" style="padding:20px 24px;margin-bottom:16px;">
    <p style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 4px;">Selamat datang, {{ $user->name }}</p>
    <p style="font-size:12.5px;color:#2563EB;margin:0;">Peran: {{ implode(', ', $daftarPeran) ?: '-' }}</p>
</div>

@if($user->isAdmin())
<div style="background:linear-gradient(135deg,#1E3A5F,#312e81);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
    <div style="color:#fff;">
        <p style="font-size:15px;font-weight:800;margin:0 0 3px;"><i class="ti ti-user-shield"></i> Panel Admin</p>
        <p style="font-size:12.5px;opacity:.85;margin:0;">Kelola data siswa, guru & role, absensi secara penuh.</p>
    </div>
    <a href="{{ route('manajemen-sekolah.data-guru') }}" class="btn" style="background:#fff;color:#1E3A5F;font-weight:700;">Buka Kelola Role &rarr;</a>
</div>
@endif

@php
    $menuUtama = [
        ['label' => 'Absensi Harian', 'icon' => 'ti-calendar-check', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'href' => route('manajemen-sekolah.absensi.index')],
        ['label' => 'Rekap Bulanan', 'icon' => 'ti-report', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'href' => route('manajemen-sekolah.absensi.rekap')],
        ['label' => 'Data Siswa', 'icon' => 'ti-users', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'href' => route('manajemen-sekolah.data-siswa')],
        ['label' => 'Data Guru', 'icon' => 'ti-user-check', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'href' => route('manajemen-sekolah.data-guru')],
    ];

    $menuGuru = [
        ['label' => 'Jadwal Mengajar', 'icon' => 'ti-clock', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'segera' => true],
        ['label' => 'Ajukan Absen Diri', 'icon' => 'ti-user-exclamation', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'segera' => true],
        ['label' => 'Guru Wali', 'icon' => 'ti-users-group', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'segera' => true],
        ['label' => 'Ajuan Surat', 'icon' => 'ti-file-text', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'segera' => true],
        ['label' => 'Laporan Keagamaan', 'icon' => 'ti-moon-stars', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'segera' => true],
        ['label' => 'Peminjaman', 'icon' => 'ti-door', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'segera' => true],
        ['label' => 'Foto Siswa', 'icon' => 'ti-photo', 'bg' => '#fbcfe8', 'warna' => '#be185d', 'segera' => true],
    ];

    $menuPiket = [
        ['label' => 'Isi Absensi', 'icon' => 'ti-pencil', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'href' => route('manajemen-sekolah.absensi.index')],
        ['label' => 'Isi Keterlambatan', 'icon' => 'ti-clock', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'segera' => true],
        ['label' => 'Siswa Terlambat', 'icon' => 'ti-alarm', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'segera' => true],
        ['label' => 'Absensi Siswa', 'icon' => 'ti-checkbox', 'bg' => '#bfdbfe', 'warna' => '#1d4ed8', 'href' => route('manajemen-sekolah.absensi.rekap')],
        ['label' => 'Arsip Surat', 'icon' => 'ti-briefcase', 'bg' => '#fde68a', 'warna' => '#a16207', 'segera' => true],
        ['label' => 'Ajuan WhatsApp', 'icon' => 'ti-brand-whatsapp', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'segera' => true],
        ['label' => 'Absensi Guru', 'icon' => 'ti-users', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'segera' => true],
    ];

    $sectionLain = [
        'is_tatib' => ['label' => 'Menu Tata Tertib', 'items' => [
            ['label' => 'Lapor Pelanggaran', 'icon' => 'ti-alert-octagon', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'href' => route('manajemen-sekolah.tatib.create')],
            ['label' => 'Rekap Poin Siswa', 'icon' => 'ti-list-details', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'href' => route('manajemen-sekolah.tatib.index')],
        ]],
        'is_bk' => ['label' => 'Menu Bimbingan Konseling', 'items' => [
            ['label' => 'Catatan Konseling', 'icon' => 'ti-notes', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'segera' => true],
        ]],
        'is_kebersihan' => ['label' => 'Menu Kebersihan', 'items' => [
            ['label' => 'Lapor Kelas Kotor', 'icon' => 'ti-spray', 'bg' => '#fde68a', 'warna' => '#a16207', 'segera' => true],
        ]],
        'is_keagamaan' => ['label' => 'Menu Keagamaan', 'items' => [
            ['label' => 'Absensi Sholat', 'icon' => 'ti-moon-stars', 'bg' => '#e9d5ff', 'warna' => '#6d28d9', 'segera' => true],
        ]],
        'is_kepsek' => ['label' => 'Menu Kepala Sekolah', 'items' => [
            ['label' => 'Setujui RPP', 'icon' => 'ti-file-check', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'segera' => true],
        ]],
        'is_kesiswaan' => ['label' => 'Menu Kesiswaan', 'items' => [
            ['label' => 'Rekap Pelanggaran', 'icon' => 'ti-alert-octagon', 'bg' => '#fecaca', 'warna' => '#b91c1c', 'href' => route('manajemen-sekolah.tatib.index')],
            ['label' => 'Data Ekstrakurikuler', 'icon' => 'ti-ball-basketball', 'bg' => '#bbf7d0', 'warna' => '#15803d', 'segera' => true],
        ]],
    ];
@endphp

@include('manajemen-sekolah.partials.menu-section', ['judul' => 'Menu Utama', 'items' => $menuUtama])

@if($bisaPiket)
@include('manajemen-sekolah.partials.menu-section', ['judul' => 'Menu Piket', 'items' => $menuPiket])
@endif

@include('manajemen-sekolah.partials.menu-section', ['judul' => 'Menu Jabatan Guru', 'items' => $menuGuru])

@foreach($sectionLain as $flag => $section)
    @if($user->isAdmin() || ($guruSaya && $guruSaya->{$flag}))
    @include('manajemen-sekolah.partials.menu-section', ['judul' => $section['label'], 'items' => $section['items']])
    @endif
@endforeach
@endsection
