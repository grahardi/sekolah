@extends('layouts.sarpras')
@section('title', 'Pengaturan Sarpras')
@section('page-title', 'Pengaturan Sarpras')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<form action="{{ route('sarpras.pengaturan.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card" style="padding:20px;max-width:500px;">
        <div style="margin-bottom:16px;">
            <label class="form-label">Prefix Kode Barang (opsional)</label>
            <input type="text" name="sarpras_prefix_kode" value="{{ $sekolah->sarpras_prefix_kode }}" class="form-input" placeholder="mis. SMP1-">
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Kalau diisi, kode barang baru jadi mis. "SMP1-000001".</p>
        </div>
        <div>
            <label class="form-label">Ambang Batas Peminjaman (hari)</label>
            <input type="number" name="sarpras_ambang_batas_pinjam_hari" value="{{ $sekolah->sarpras_ambang_batas_pinjam_hari ?? 7 }}" min="1" max="365" class="form-input" required>
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Dipakai sebagai default "rencana kembali" saat catat peminjaman baru.</p>
        </div>
    </div>

    <div style="margin-top:16px;max-width:500px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Pengaturan</button>
    </div>
</form>

@endsection
