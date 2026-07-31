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

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
    <div class="card" style="padding:18px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Mata Pelajaran</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $totalMapel }}</p>
    </div>
    <div class="card" style="padding:18px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Wali Kelas</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $totalWaliKelas }}</p>
    </div>
    <div class="card" style="padding:18px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Guru Pengajar</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $totalGuruPengajar }}</p>
    </div>
    <div class="card" style="padding:18px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Guru Ekstrakurikuler</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $totalGuruEkstrakurikuler }}</p>
    </div>
    <div class="card" style="padding:18px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Guru Kokurikuler</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $totalGuruKokurikuler }}</p>
    </div>
</div>

<div style="margin-top:24px;padding:18px;background:#f8fafc;border-radius:10px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 6px;">Langkah Awal Setup</p>
    <ol style="font-size:13px;color:#475569;margin:0;padding-left:18px;line-height:1.8;">
        <li>Atur <a href="{{ route('erapor.tahun-ajaran') }}" style="color:#2563EB;">Tahun Ajaran</a> aktif</li>
        <li>Tambah daftar <a href="{{ route('erapor.mata-pelajaran') }}" style="color:#2563EB;">Mata Pelajaran</a></li>
        <li>Tetapkan <a href="{{ route('erapor.penugasan') }}" style="color:#2563EB;">Wali Kelas &amp; Guru Pengajar</a> per kelas</li>
        <li>Input nilai &amp; cetak rapor <em>(segera hadir)</em></li>
    </ol>
</div>
@endsection
