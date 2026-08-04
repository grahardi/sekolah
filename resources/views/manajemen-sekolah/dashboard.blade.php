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
        foreach (['is_piket'=>'piket','is_tatib'=>'tatib','is_bk'=>'bk','is_kebersihan'=>'kebersihan','is_keagamaan'=>'keagamaan','is_kepsek'=>'kepsek'] as $flag=>$label) {
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
        ['label' => 'Absensi Harian', 'icon' => 'ti-calendar-check', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.index')],
        ['label' => 'Rekap Bulanan', 'icon' => 'ti-report', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.rekap')],
        ['label' => 'Data Siswa', 'icon' => 'ti-users', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'href' => route('manajemen-sekolah.data-siswa')],
        ['label' => 'Data Guru', 'icon' => 'ti-user-check', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'href' => route('manajemen-sekolah.data-guru')],
    ];

    $menuGuru = [
        ['label' => 'Jadwal Mengajar', 'icon' => 'ti-clock', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'segera' => true],
        ['label' => 'Ajukan Absen Diri', 'icon' => 'ti-user-exclamation', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ['label' => 'Guru Wali', 'icon' => 'ti-users-group', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ['label' => 'Ajuan Surat', 'icon' => 'ti-file-text', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ['label' => 'Laporan Keagamaan', 'icon' => 'ti-moon-stars', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ['label' => 'Peminjaman', 'icon' => 'ti-door', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
        ['label' => 'Foto Siswa', 'icon' => 'ti-photo', 'bg' => '#fce7f3', 'warna' => '#db2777', 'segera' => true],
    ];

    $menuPiket = [
        ['label' => 'Isi Absensi', 'icon' => 'ti-pencil', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.index')],
        ['label' => 'Isi Keterlambatan', 'icon' => 'ti-clock', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ['label' => 'Siswa Terlambat', 'icon' => 'ti-alarm', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ['label' => 'Absensi Siswa', 'icon' => 'ti-checkbox', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.rekap')],
        ['label' => 'Arsip Surat', 'icon' => 'ti-briefcase', 'bg' => '#fffbeb', 'warna' => '#d97706', 'segera' => true],
        ['label' => 'Ajuan WhatsApp', 'icon' => 'ti-brand-whatsapp', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
        ['label' => 'Absensi Guru', 'icon' => 'ti-users', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
    ];

    $sectionLain = [
        'is_tatib' => ['label' => 'Menu Tata Tertib', 'items' => [
            ['label' => 'Lapor Pelanggaran', 'icon' => 'ti-alert-octagon', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
            ['label' => 'Rekap Poin Siswa', 'icon' => 'ti-list-details', 'bg' => '#fef2f2', 'warna' => '#dc2626', 'segera' => true],
        ]],
        'is_bk' => ['label' => 'Menu Bimbingan Konseling', 'items' => [
            ['label' => 'Catatan Konseling', 'icon' => 'ti-notes', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ]],
        'is_kebersihan' => ['label' => 'Menu Kebersihan', 'items' => [
            ['label' => 'Lapor Kelas Kotor', 'icon' => 'ti-spray', 'bg' => '#fffbeb', 'warna' => '#d97706', 'segera' => true],
        ]],
        'is_keagamaan' => ['label' => 'Menu Keagamaan', 'items' => [
            ['label' => 'Absensi Sholat', 'icon' => 'ti-moon-stars', 'bg' => '#f3e8ff', 'warna' => '#7C3AED', 'segera' => true],
        ]],
        'is_kepsek' => ['label' => 'Menu Kepala Sekolah', 'items' => [
            ['label' => 'Setujui RPP', 'icon' => 'ti-file-check', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'segera' => true],
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
