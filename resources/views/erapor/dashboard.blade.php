@extends('layouts.erapor')
@section('title', 'E-Rapor')
@section('page-title', 'E-Rapor')

@section('content')
@if($tahunAktif)
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:18px;">
    <p style="font-size:13px;color:#1e40af;margin:0;"><i class="ti ti-calendar-event"></i> Tahun ajaran aktif: <strong>{{ $tahunAktif->label }}</strong></p>
</div>
@else
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:18px;">
    <p style="font-size:13px;color:#92400e;margin:0;">Belum ada tahun ajaran aktif. <a href="{{ route('erapor.tahun-ajaran') }}" style="text-decoration:underline;">Atur di sini</a>.</p>
</div>
@endif

@php
    $statCards = [
        ['label' => 'Pengguna', 'value' => $totalPengguna, 'icon' => 'ti-user-shield', 'iconColor' => '#7C3AED', 'bg' => '#f5f3ff'],
        ['label' => 'Siswa Aktif', 'value' => $totalSiswa, 'icon' => 'ti-users', 'iconColor' => '#2563EB', 'bg' => '#eff6ff'],
        ['label' => 'Kelas', 'value' => $totalKelas, 'icon' => 'ti-door', 'iconColor' => '#0891b2', 'bg' => '#ecfeff'],
        ['label' => 'Mata Pelajaran', 'value' => $totalMapel, 'icon' => 'ti-book-2', 'iconColor' => '#d97706', 'bg' => '#fffbeb'],
        ['label' => 'Wali Kelas', 'value' => $totalWaliKelas, 'icon' => 'ti-user-check', 'iconColor' => '#16a34a', 'bg' => '#f0fdf4'],
        ['label' => 'Guru Pengajar', 'value' => $totalGuruPengajar, 'icon' => 'ti-chalkboard', 'iconColor' => '#db2777', 'bg' => '#fdf2f8'],
        ['label' => 'Guru Ekstrakurikuler', 'value' => $totalGuruEkstrakurikuler, 'icon' => 'ti-ball-basketball', 'iconColor' => '#ea580c', 'bg' => '#fff7ed'],
        ['label' => 'Guru Kokurikuler', 'value' => $totalGuruKokurikuler, 'icon' => 'ti-puzzle', 'iconColor' => '#0d9488', 'bg' => '#f0fdfa'],
        ['label' => 'Tujuan Pembelajaran', 'value' => $totalTp, 'icon' => 'ti-target-arrow', 'iconColor' => '#4f46e5', 'bg' => '#eef2ff'],
        ['label' => 'Penilaian Dibuat', 'value' => $totalPenilaian, 'icon' => 'ti-report', 'iconColor' => '#c026d3', 'bg' => '#fdf4ff'],
    ];
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:20px;">
    @foreach($statCards as $s)
    <div style="background:{{ $s['bg'] }};border:1px solid rgba(0,0,0,0.04);border-radius:14px;padding:16px;">
        <div style="width:34px;height:34px;border-radius:9px;background:#fff;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
            <i class="ti {{ $s['icon'] }}" style="color:{{ $s['iconColor'] }};font-size:17px;"></i>
        </div>
        <p style="font-size:24px;font-weight:800;color:#0f172a;margin:0;">{{ $s['value'] }}</p>
        <p style="font-size:11px;color:#64748b;margin:2px 0 0;">{{ $s['label'] }}</p>
    </div>
    @endforeach
</div>

@if($tahunAktif)
<div style="background:linear-gradient(135deg,#0891b2,#2563EB);border-radius:14px;padding:20px 22px;margin-bottom:20px;color:#fff;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div>
            <p style="font-size:12px;opacity:.85;margin:0 0 4px;text-transform:uppercase;letter-spacing:.03em;">Kelengkapan Rapor Semester Ini</p>
            <p style="font-size:13px;opacity:.9;margin:0;">{{ $totalRaporSemester }} dari {{ $totalSiswa }} siswa sudah punya rapor</p>
        </div>
        <p style="font-size:34px;font-weight:800;margin:0;">{{ $kelengkapanRapor }}%</p>
    </div>
    <div style="background:rgba(255,255,255,.25);border-radius:999px;height:8px;overflow:hidden;margin-top:12px;">
        <div style="width:{{ $kelengkapanRapor }}%;height:100%;background:#fff;"></div>
    </div>
</div>
@endif

<div style="padding:18px;background:#f8fafc;border-radius:10px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 6px;">Langkah Awal Setup</p>
    <ol style="font-size:13px;color:#475569;margin:0;padding-left:18px;line-height:1.8;">
        <li>Atur <a href="{{ route('erapor.tahun-ajaran') }}" style="color:#2563EB;">Tahun Ajaran</a> aktif</li>
        <li>Tambah daftar <a href="{{ route('erapor.mata-pelajaran') }}" style="color:#2563EB;">Mata Pelajaran</a></li>
        <li>Tetapkan <a href="{{ route('erapor.penugasan') }}" style="color:#2563EB;">Wali Kelas &amp; Guru Pengajar</a> per kelas</li>
        <li>Input <a href="{{ route('erapor.penilaian.index') }}" style="color:#2563EB;">nilai</a> &amp; <a href="{{ route('erapor.rapor.index') }}" style="color:#2563EB;">cetak rapor</a></li>
    </ol>
</div>
@endsection
