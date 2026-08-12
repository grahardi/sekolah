@extends('layouts.app')
@section('title', 'Hasil Scan - ' . $siswa->nama_lengkap)
@section('page-title', 'Hasil Scan KK & Akta')

@section('header-actions')
    <a href="{{ route('siswa.scan-kk.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'ocr'])

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

@if(! $hasil->exists)
<div style="background:#eff6ff;color:#1e40af;padding:14px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;justify-content:space-between;">
    <span><i class="ti ti-info-circle"></i> Siswa ini belum pernah discan.</span>
    <form action="{{ route('siswa.scan-kk.satu', $siswa) }}" method="POST" onsubmit="return confirm('Scan berkas KK/Akta siswa ini sekarang?')">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-scan"></i> Scan Sekarang</button>
    </form>
</div>
@else
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
    <form action="{{ route('siswa.scan-kk.satu', $siswa) }}" method="POST" onsubmit="return confirm('Scan ulang berkas KK/Akta siswa ini?')">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Scan Ulang</button>
    </form>
</div>
@endif

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:-6px 0 16px;">{{ $siswa->nama_lengkap }} &middot; {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- Kartu Keluarga --}}
    <div class="card" style="padding:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-id" style="color:#2563eb;"></i> Kartu Keluarga</p>
            @if($hasil->status_kk === 'ok')
            <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Skor: {{ $hasil->skor_kk }}%</span>
            @elseif($hasil->status_kk === 'error')
            <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Error</span>
            @else
            <span style="color:#94a3b8;font-size:11px;">Belum discan</span>
            @endif
        </div>

        @if($hasil->status_kk === 'ok')
        @php
        $detailAyah = $hasil->detailAyahHasilScan();
        $detailIbu = $hasil->detailIbuHasilScan();
        @endphp
        <div style="font-size:13px;">
            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:10px 0 4px;">Data Ayah</p>
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Nama Ayah', 'nilaiInduk' => $siswa->nama_ayah, 'nilaiScan' => $hasil->namaAyahHasilScan(), 'fieldTujuan' => 'nama_ayah', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'NIK Ayah', 'nilaiInduk' => $siswa->nik_ayah, 'nilaiScan' => $detailAyah['nik'] ?? null, 'fieldTujuan' => 'nik_ayah', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tahun Lahir Ayah', 'nilaiInduk' => $siswa->tahun_lahir_ayah, 'nilaiScan' => \App\Models\ScanKkHasil::ekstrakTahun($detailAyah['tanggal_lahir'] ?? null), 'fieldTujuan' => 'tahun_lahir_ayah', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Pekerjaan Ayah', 'nilaiInduk' => $siswa->pekerjaan_ayah, 'nilaiScan' => $detailAyah['jenis_pekerjaan'] ?? null, 'fieldTujuan' => 'pekerjaan_ayah', 'siswa' => $siswa])

            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:14px 0 4px;">Data Ibu</p>
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Nama Ibu', 'nilaiInduk' => $siswa->nama_ibu, 'nilaiScan' => $hasil->namaIbuHasilScan(), 'fieldTujuan' => 'nama_ibu', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'NIK Ibu', 'nilaiInduk' => $siswa->nik_ibu, 'nilaiScan' => $detailIbu['nik'] ?? null, 'fieldTujuan' => 'nik_ibu', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tahun Lahir Ibu', 'nilaiInduk' => $siswa->tahun_lahir_ibu, 'nilaiScan' => \App\Models\ScanKkHasil::ekstrakTahun($detailIbu['tanggal_lahir'] ?? null), 'fieldTujuan' => 'tahun_lahir_ibu', 'siswa' => $siswa])
            @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Pekerjaan Ibu', 'nilaiInduk' => $siswa->pekerjaan_ibu, 'nilaiScan' => $detailIbu['jenis_pekerjaan'] ?? null, 'fieldTujuan' => 'pekerjaan_ibu', 'siswa' => $siswa])

            <details style="margin-top:12px;">
                <summary style="font-size:11px;color:#94a3b8;cursor:pointer;">Lihat data lengkap hasil OCR</summary>
                <pre style="font-size:10px;background:#f8fafc;padding:10px;border-radius:6px;overflow-x:auto;margin-top:6px;">{{ json_encode($hasil->data_kk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>
        @elseif($hasil->status_kk === 'error')
        <p style="font-size:12px;color:#991b1b;">{{ $hasil->pesan_error }}</p>
        @else
        <p style="font-size:12px;color:#94a3b8;">Belum ada berkas KK atau belum discan.</p>
        @endif
    </div>

    {{-- Akta Lahir --}}
    <div class="card" style="padding:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-certificate" style="color:#16a34a;"></i> Akta Lahir</p>
            @if($hasil->status_akta === 'ok')
            <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Skor: {{ $hasil->skor_akta }}%</span>
            @elseif($hasil->status_akta === 'error')
            <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Error</span>
            @else
            <span style="color:#94a3b8;font-size:11px;">Belum discan</span>
            @endif
        </div>

        @if($hasil->status_akta === 'ok')
        <p style="font-size:11px;color:#94a3b8;margin:0;">No. Registrasi Akta (hasil scan)</p>
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:2px 0 0;font-family:monospace;">{{ $hasil->no_akta }}</p>
        @elseif($hasil->status_akta === 'error')
        <p style="font-size:12px;color:#991b1b;">{{ $hasil->pesan_error }}</p>
        @else
        <p style="font-size:12px;color:#94a3b8;">Belum ada berkas Akta atau belum discan.</p>
        @endif
    </div>
</div>

@endsection
