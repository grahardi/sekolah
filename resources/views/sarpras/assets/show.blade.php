@extends('layouts.sarpras')
@section('title', $asset->nama_barang)
@section('page-title', $asset->nama_barang)

@section('content')
<div style="max-width:640px;">
    <div class="card" style="padding:20px;margin-bottom:16px;">
        <div style="display:flex;gap:16px;">
            @if($asset->foto)
            <img src="{{ $asset->foto_url }}" style="width:110px;height:110px;object-fit:cover;border-radius:10px;flex-shrink:0;">
            @else
            <div style="width:110px;height:110px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti {{ $asset->category?->icon ?: 'ti-box' }}" style="font-size:32px;color:#94a3b8;"></i>
            </div>
            @endif
            <div style="flex:1;">
                <p style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 4px;">{{ $asset->nama_barang }}</p>
                <p style="font-size:12px;font-family:monospace;color:#64748b;margin:0 0 8px;">{{ $asset->kode_barang }}</p>
                @php
                $statusWarna = ['baik' => ['bg' => '#dcfce7', 'txt' => '#166534'], 'rusak' => ['bg' => '#fee2e2', 'txt' => '#991b1b'], 'dalam_perbaikan' => ['bg' => '#fef3c7', 'txt' => '#92400e']][$asset->status];
                $statusLabel = ['baik' => 'Baik', 'rusak' => 'Rusak', 'dalam_perbaikan' => 'Dalam Perbaikan'][$asset->status];
                @endphp
                <span style="background:{{ $statusWarna['bg'] }};color:{{ $statusWarna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $statusLabel }}</span>
            </div>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <div style="display:grid;grid-template-columns:140px 1fr;gap:10px;font-size:13px;">
            <span style="color:#64748b;">Kategori</span><span style="color:#0f172a;">{{ $asset->category?->name ?? '-' }}</span>
            <span style="color:#64748b;">Lokasi</span><span style="color:#0f172a;">{{ $asset->location?->name ?? '-' }}</span>
            <span style="color:#64748b;">Kode Umum</span><span style="color:#0f172a;">{{ $asset->kode_umum ?: '-' }}</span>
            <span style="color:#64748b;">Kode Aset</span><span style="color:#0f172a;">{{ $asset->kode_aset ?: '-' }}</span>
            <span style="color:#64748b;">Tahun Pembelian</span><span style="color:#0f172a;">{{ $asset->tahun_pembelian ?: '-' }}</span>
            <span style="color:#64748b;">Sumber Dana</span><span style="color:#0f172a;">{{ $asset->fundingSource?->name ?? '-' }}</span>
            <span style="color:#64748b;">Keterangan</span><span style="color:#0f172a;">{{ $asset->keterangan ?: '-' }}</span>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:16px;">
        <a href="{{ route('sarpras.assets.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Kembali</a>
        <a href="{{ route('sarpras.assets.edit', $asset) }}" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="ti ti-pencil"></i> Edit</a>
    </div>
</div>
@endsection
