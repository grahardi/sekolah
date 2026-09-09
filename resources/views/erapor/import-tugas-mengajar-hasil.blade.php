@extends('layouts.erapor')
@section('title', 'Hasil Import Tugas Mengajar')
@section('page-title', 'Hasil Import Tugas Mengajar')

@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:14px;margin-bottom:20px;">
    <div class="card" style="padding:16px;text-align:center;background:#f0fdf4;border-color:#bbf7d0;">
        <p style="font-size:26px;font-weight:800;color:#16a34a;margin:0;">{{ $dibuat }}</p>
        <p style="font-size:11px;color:#166534;margin:4px 0 0;">Tugas Mengajar Baru Ditambahkan</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;background:#f8fafc;">
        <p style="font-size:26px;font-weight:800;color:#64748b;margin:0;">{{ $sudahAda }}</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Sudah Ada Sebelumnya (dilewati)</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;background:#eff6ff;border-color:#bfdbfe;">
        <p style="font-size:26px;font-weight:800;color:#1d4ed8;margin:0;">{{ count($mapelBaru) }}</p>
        <p style="font-size:11px;color:#1e40af;margin:4px 0 0;">Mapel Baru Dibuat</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;background:{{ count($guruTidakKetemu) ? '#fef2f2' : '#f8fafc' }};border-color:{{ count($guruTidakKetemu) ? '#fecaca' : '#e9ecef' }};">
        <p style="font-size:26px;font-weight:800;color:{{ count($guruTidakKetemu) ? '#dc2626' : '#64748b' }};margin:0;">{{ count($guruTidakKetemu) }}</p>
        <p style="font-size:11px;color:{{ count($guruTidakKetemu) ? '#991b1b' : '#64748b' }};margin:4px 0 0;">Nama Guru Tidak Ketemu</p>
    </div>
</div>

@if($sudahAda > 0)
<div class="card" style="padding:16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 10px;">Detail Sudah Ada Sebelumnya ({{ $sudahAda }})</p>
    <p style="font-size:12px;color:#94a3b8;margin:0 0 10px;">Kombinasi kelas-mapel-guru ini sudah tercatat sebelumnya di tahun ajaran ini, tidak dibuat ulang.</p>
    <div style="max-height:200px;overflow-y:auto;">
        @foreach($sudahAdaDetail as $d)
        <p style="font-size:12px;color:#475569;margin:3px 0;">{{ $d }}</p>
        @endforeach
    </div>
</div>
@endif

@if(count($doublePengajar) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;background:#fdf2f8;border-color:#fbcfe8;">
    <p style="font-size:13px;font-weight:700;color:#9d174d;margin:0 0 10px;"><i class="ti ti-users"></i> Kelas dengan Lebih dari 1 Pengajar untuk Mapel yang Sama ({{ count($doublePengajar) }})</p>
    <p style="font-size:12px;color:#831843;margin:0 0 10px;">Ditemukan di file Excel-nya sendiri - kelas & mapel yang sama tercatat diajar oleh guru berbeda-beda pada baris yang berlainan. Cek lagi datanya, mungkin salah satu salah ketik/salah baris.</p>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr style="background:#fce7f3;"><th style="padding:6px 10px;text-align:left;font-size:11px;color:#9d174d;">Kelas</th><th style="padding:6px 10px;text-align:left;font-size:11px;color:#9d174d;">Mapel</th><th style="padding:6px 10px;text-align:left;font-size:11px;color:#9d174d;">Guru yang Tercatat</th></tr></thead>
        <tbody>
            @foreach($doublePengajar as $dp)
            <tr style="border-top:1px solid #fbcfe8;">
                <td style="padding:6px 10px;font-size:13px;color:#831843;">{{ $dp['kelas'] }}{{ $dp['rombel'] ? "-{$dp['rombel']}" : '' }}</td>
                <td style="padding:6px 10px;font-size:13px;color:#831843;">{{ $dp['mapel'] }}</td>
                <td style="padding:6px 10px;font-size:13px;color:#831843;">{{ implode(', ', $dp['guru']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(count($kelasBolong) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;background:#fff7ed;border-color:#fed7aa;">
    <p style="font-size:13px;font-weight:700;color:#9a3412;margin:0 0 10px;"><i class="ti ti-alert-circle"></i> Kelas yang Belum Punya Pengajar untuk Mapel Tertentu ({{ count($kelasBolong) }})</p>
    <p style="font-size:12px;color:#7c2d12;margin:0 0 10px;">Mapel ini muncul di kelas lain tapi kelas berikut belum kepasang gurunya - biasanya krn nama gurunya gak ketemu/gak dicentang tadi. Bisa ditambahkan manual lewat menu Tugas Mengajar.</p>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr style="background:#ffedd5;"><th style="padding:6px 10px;text-align:left;font-size:11px;color:#9a3412;">Kelas</th><th style="padding:6px 10px;text-align:left;font-size:11px;color:#9a3412;">Mapel Belum Ada Pengajar</th></tr></thead>
        <tbody>
            @foreach($kelasBolong as $kb)
            <tr style="border-top:1px solid #fed7aa;">
                <td style="padding:6px 10px;font-size:13px;color:#7c2d12;">{{ $kb['kelas'] }}{{ $kb['rombel'] ? "-{$kb['rombel']}" : '' }}</td>
                <td style="padding:6px 10px;font-size:13px;color:#7c2d12;">{{ $kb['mapel'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if(count($mapelBaru) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 10px;">Mapel Baru yang Dibuat</p>
    <p style="font-size:12px;color:#94a3b8;margin:0 0 10px;">Namanya diambil dari singkatan yang belum dikenali sistem - cek & ganti nama lengkapnya lewat menu Mata Pelajaran kalau perlu.</p>
    @foreach($mapelBaru as $m)
    <span style="display:inline-block;background:#eff6ff;color:#1e40af;font-size:12px;padding:4px 10px;border-radius:20px;margin:2px;">{{ $m }}</span>
    @endforeach
</div>
@endif

@if(count($guruTidakKetemu) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;background:#fef2f2;border-color:#fecaca;">
    <p style="font-size:13px;font-weight:700;color:#991b1b;margin:0 0 10px;"><i class="ti ti-alert-triangle"></i> Nama Guru Tidak Ketemu di Sistem</p>
    <p style="font-size:12px;color:#7f1d1d;margin:0 0 10px;">Baris dengan nama ini DILEWATI (tidak dibuat tugas mengajarnya). Cek ejaan namanya di Excel, atau pastikan guru ybs sudah terdaftar (lewat Kepegawaian atau menu Guru), lalu import ulang.</p>
    <table style="width:100%;border-collapse:collapse;">
        <thead><tr style="background:#fee2e2;"><th style="padding:6px 10px;text-align:left;font-size:11px;color:#991b1b;">Nama di Excel</th><th style="padding:6px 10px;text-align:left;font-size:11px;color:#991b1b;">Jumlah Baris Dilewati</th></tr></thead>
        <tbody>
            @foreach($guruTidakKetemu as $nama => $jumlah)
            <tr style="border-top:1px solid #fecaca;"><td style="padding:6px 10px;font-size:13px;color:#7f1d1d;">{{ $nama }}</td><td style="padding:6px 10px;font-size:13px;color:#7f1d1d;">{{ $jumlah }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<a href="{{ route('erapor.tugas-mengajar.import-form') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Import File Lain</a>
<a href="{{ route('erapor.penugasan') }}" class="btn btn-primary"><i class="ti ti-list-check"></i> Lihat Daftar Tugas Mengajar</a>

@endsection
