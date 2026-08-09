@extends('layouts.sarpras')
@section('title', 'Data Barang')
@section('page-title', 'Data Barang / Inventaris')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;margin-bottom:16px;">
    <div style="flex:1;min-width:200px;">
        <label class="form-label">Cari</label>
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-input" placeholder="Nama atau kode barang...">
    </div>
    <div style="min-width:160px;">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ ($filters['category_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div style="min-width:160px;">
        <label class="form-label">Status</label>
        <select name="status" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="baik" {{ ($filters['status'] ?? '') === 'baik' ? 'selected' : '' }}>Baik</option>
            <option value="rusak" {{ ($filters['status'] ?? '') === 'rusak' ? 'selected' : '' }}>Rusak</option>
            <option value="dalam_perbaikan" {{ ($filters['status'] ?? '') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
        </select>
    </div>
    <button type="submit" class="btn btn-secondary"><i class="ti ti-search"></i> Cari</button>
    <a href="{{ route('sarpras.assets.create') }}" class="btn btn-primary" style="margin-left:auto;"><i class="ti ti-plus"></i> Tambah Barang</a>
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 14px;text-align:left;width:50px;"></th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;color:#64748b;">Kode</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;color:#64748b;">Nama Barang</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;color:#64748b;">Kategori</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;color:#64748b;">Lokasi</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 14px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $a)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:8px 14px;">
                    @if($a->foto)
                    <img src="{{ $a->foto_url }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
                    @else
                    <div style="width:36px;height:36px;background:#f1f5f9;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="ti {{ $a->category?->icon ?: 'ti-box' }}" style="color:#94a3b8;font-size:16px;"></i>
                    </div>
                    @endif
                </td>
                <td style="padding:8px 14px;font-size:12px;font-family:monospace;color:#475569;">{{ $a->kode_barang }}</td>
                <td style="padding:8px 14px;font-size:13px;">
                    <a href="{{ route('sarpras.assets.show', $a) }}" style="color:#0f172a;font-weight:500;text-decoration:none;">{{ $a->nama_barang }}</a>
                </td>
                <td style="padding:8px 14px;font-size:12px;color:#64748b;">{{ $a->category?->name ?? '-' }}</td>
                <td style="padding:8px 14px;font-size:12px;color:#64748b;">{{ $a->location?->name ?? '-' }}</td>
                <td style="padding:8px 14px;">
                    @php
                    $statusWarna = ['baik' => ['bg' => '#dcfce7', 'txt' => '#166534'], 'rusak' => ['bg' => '#fee2e2', 'txt' => '#991b1b'], 'dalam_perbaikan' => ['bg' => '#fef3c7', 'txt' => '#92400e']][$a->status];
                    $statusLabel = ['baik' => 'Baik', 'rusak' => 'Rusak', 'dalam_perbaikan' => 'Dalam Perbaikan'][$a->status];
                    @endphp
                    <span style="background:{{ $statusWarna['bg'] }};color:{{ $statusWarna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $statusLabel }}</span>
                </td>
                <td style="padding:8px 14px;text-align:right;white-space:nowrap;">
                    <a href="{{ route('sarpras.assets.edit', $a) }}" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></a>
                    <form action="{{ route('sarpras.assets.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $assets->links() }}</div>

@endsection
