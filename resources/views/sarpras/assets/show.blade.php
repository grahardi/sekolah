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

    <div class="card" style="padding:20px;margin-bottom:16px;text-align:center;">
        <p style="font-size:12px;font-weight:700;color:#0f172a;margin:0 0 10px;">QR Code Barang (utk Scan)</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($asset->kode_barang) }}" alt="QR" style="margin:0 auto;">
        <p style="font-size:11px;color:#94a3b8;margin:8px 0 0;">Cetak & tempel di barang, scan lewat menu "Scan QR Barang"</p>
    </div>

    @php $peminjamanAktif = $asset->peminjaman->firstWhere('status', 'dipinjam'); @endphp
    @if($peminjamanAktif)
    <div class="card" style="padding:16px;margin-bottom:16px;background:#eff6ff;border-color:#bfdbfe;">
        <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0 0 4px;"><i class="ti ti-transfer"></i> Sedang Dipinjam</p>
        <p style="font-size:13px;color:#1e3a8a;margin:0;">{{ $peminjamanAktif->peminjam_nama }} - rencana kembali {{ $peminjamanAktif->tanggal_kembali_rencana->format('d/m/Y') }}</p>
    </div>
    @endif

    @if($asset->riwayatKerusakan->isNotEmpty())
    <div class="card" style="padding:16px;margin-bottom:16px;">
        <p style="font-size:12px;font-weight:700;color:#0f172a;margin:0 0 10px;">Riwayat Kerusakan</p>
        @foreach($asset->riwayatKerusakan as $rk)
        <div style="padding:8px 0;border-top:1px solid #f1f5f9;font-size:12px;">
            <span style="color:#94a3b8;">{{ $rk->tanggal_lapor->format('d/m/Y') }}</span> - {{ $rk->deskripsi_kerusakan }} ({{ $rk->labelStatus() }})
        </div>
        @endforeach
    </div>
    @endif

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
