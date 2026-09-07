@extends('layouts.app')
@section('title', 'Pengaturan Buku Induk')
@section('page-title', 'Pengaturan Buku Induk')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<form action="{{ route('siswa.pengaturan.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card" style="padding:20px;max-width:500px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Tanggal Cetak Biodata Rapor</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Dipakai untuk tanda tangan di cetak "Biodata Rapor" (perorangan maupun massal). Kosongkan supaya otomatis pakai tanggal hari ini.</p>
        <input type="date" name="biodata_tanggal_manual" value="{{ $sekolah->biodata_tanggal_manual?->format('Y-m-d') }}" class="form-input">
    </div>

    <div class="card" style="padding:20px;max-width:500px;margin-top:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Watermark</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Tampilkan teks watermark transparan di cetak Buku Induk & Biodata Rapor. Default nonaktif.</p>

        <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;cursor:pointer;">
            <input type="checkbox" name="watermark_aktif" value="1" {{ $sekolah->watermark_aktif ? 'checked' : '' }}>
            <span style="font-size:13px;color:#374151;">Aktifkan watermark</span>
        </label>

        <label class="form-label">Teks Watermark</label>
        <input type="text" name="watermark_teks" value="{{ $sekolah->watermark_teks }}" class="form-input" placeholder="mis. {{ auth()->user()->sekolah->nama ?? 'Nama Sekolah' }}" style="margin-bottom:14px;">

        <label class="form-label">Transparansi (1 = paling samar, 100 = paling pekat)</label>
        <input type="number" name="watermark_transparansi" value="{{ $sekolah->watermark_transparansi ?? 10 }}" min="1" max="100" class="form-input">
    </div>

    <div style="margin-top:16px;max-width:500px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Pengaturan</button>
    </div>
</form>

@endsection
