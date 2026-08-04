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
    <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">Menu</p>
    @php
        $guruSayaDb = auth()->user()->isAdmin() ? null : \App\Models\Guru::where('user_id', auth()->id())->first();
        $bisaAksesPiketDb = auth()->user()->isAdmin() || ($guruSayaDb && $guruSayaDb->is_piket);

        $menuIkon = [
            ['label' => 'Absensi Harian', 'icon' => 'ti-calendar-check', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.index')],
            ['label' => 'Rekap Bulanan', 'icon' => 'ti-report', 'bg' => '#eff6ff', 'warna' => '#2563EB', 'href' => route('manajemen-sekolah.absensi.rekap')],
            ['label' => 'Data Siswa', 'icon' => 'ti-users', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'href' => route('manajemen-sekolah.data-siswa')],
            ['label' => 'Data Guru', 'icon' => 'ti-user-check', 'bg' => '#f0fdf4', 'warna' => '#16a34a', 'href' => route('manajemen-sekolah.data-guru')],
        ];
        if ($bisaAksesPiketDb) {
            $menuIkon[] = ['label' => 'Menu Piket', 'icon' => 'ti-shield-check', 'bg' => '#eef2ff', 'warna' => '#4F46E5', 'href' => route('manajemen-sekolah.menu-piket')];
        }
        $menuSegera = [
            ['label' => 'Tata Tertib', 'icon' => 'ti-alert-octagon', 'bg' => '#fef2f2', 'warna' => '#dc2626'],
            ['label' => 'Bimbingan Konseling', 'icon' => 'ti-notes', 'bg' => '#f3e8ff', 'warna' => '#7C3AED'],
            ['label' => 'Kebersihan Kelas', 'icon' => 'ti-spray', 'bg' => '#fffbeb', 'warna' => '#d97706'],
            ['label' => 'Peminjaman Ruang', 'icon' => 'ti-door', 'bg' => '#fce7f3', 'warna' => '#db2777'],
        ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;">
        @foreach($menuIkon as $m)
        <a href="{{ $m['href'] }}" style="text-decoration:none;border-radius:14px;padding:20px 12px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:8px;">
            <span style="width:42px;height:42px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
                <i class="ti {{ $m['icon'] }}" style="font-size:19px;color:{{ $m['warna'] }};"></i>
            </span>
            <span style="font-size:12.5px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
        </a>
        @endforeach
        @foreach($menuSegera as $m)
        <div class="sb-item-demo" style="border-radius:14px;padding:20px 12px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:8px;">
            <span style="width:42px;height:42px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
                <i class="ti {{ $m['icon'] }}" style="font-size:19px;color:{{ $m['warna'] }};"></i>
            </span>
            <span style="font-size:12.5px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
            <span class="sb-demo-badge">Segera</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
