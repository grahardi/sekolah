@extends('layouts.sarpras')
@section('title', 'Riwayat Kerusakan')
@section('page-title', 'Riwayat Kerusakan')

@section('header-actions')
    <button type="button" onclick="document.getElementById('modal-lapor').style.display='flex'" class="btn btn-primary"><i class="ti ti-plus"></i> Lapor Kerusakan</button>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<form method="GET" style="margin-bottom:16px;max-width:240px;">
    <select name="status" class="form-input" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="dilaporkan" {{ ($filters['status'] ?? '') === 'dilaporkan' ? 'selected' : '' }}>Dilaporkan</option>
        <option value="diperbaiki" {{ ($filters['status'] ?? '') === 'diperbaiki' ? 'selected' : '' }}>Sudah Diperbaiki</option>
        <option value="tidak_bisa_diperbaiki" {{ ($filters['status'] ?? '') === 'tidak_bisa_diperbaiki' ? 'selected' : '' }}>Tidak Bisa Diperbaiki</option>
    </select>
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Barang</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Tanggal Lapor</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Deskripsi</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $r)
            @php
            $warna = ['dilaporkan' => ['bg' => '#fef9c3', 'txt' => '#854d0e'], 'diperbaiki' => ['bg' => '#dcfce7', 'txt' => '#166534'], 'tidak_bisa_diperbaiki' => ['bg' => '#fef2f2', 'txt' => '#991b1b']][$r->status];
            @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $r->asset->nama_barang ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $r->tanggal_lapor->format('d/m/Y') }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;max-width:280px;">{{ $r->deskripsi_kerusakan }}</td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $r->labelStatus() }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;">
                    @if($r->status === 'dilaporkan')
                    <button type="button" onclick="document.getElementById('modal-update-{{ $r->id }}').style.display='flex'" class="btn btn-secondary btn-sm">Update Status</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada catatan kerusakan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $riwayat->links() }}</div>

<div id="modal-lapor" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Lapor Kerusakan Barang</p>
            <button type="button" onclick="document.getElementById('modal-lapor').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('sarpras.riwayat-kerusakan.store') }}" method="POST">
            @csrf
            <label class="form-label">Barang</label>
            <select name="asset_id" class="form-input" required style="margin-bottom:12px;">
                <option value="">-- Pilih Barang --</option>
                @foreach($assets as $a)<option value="{{ $a->id }}">{{ $a->nama_barang }} ({{ $a->kode_barang }})</option>@endforeach
            </select>
            <label class="form-label">Tanggal Lapor</label>
            <input type="date" name="tanggal_lapor" value="{{ date('Y-m-d') }}" class="form-input" required style="margin-bottom:12px;">
            <label class="form-label">Deskripsi Kerusakan</label>
            <textarea name="deskripsi_kerusakan" rows="3" class="form-input" required style="margin-bottom:16px;"></textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan</button>
        </form>
    </div>
</div>

@foreach($riwayat as $r)
@if($r->status === 'dilaporkan')
<div id="modal-update-{{ $r->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Update Status - {{ $r->asset->nama_barang }}</p>
            <button type="button" onclick="document.getElementById('modal-update-{{ $r->id }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('sarpras.riwayat-kerusakan.update', $r) }}" method="POST">
            @csrf
            @method('PUT')
            <label class="form-label">Status</label>
            <select name="status" class="form-input" required style="margin-bottom:12px;">
                <option value="diperbaiki">Sudah Diperbaiki</option>
                <option value="tidak_bisa_diperbaiki">Tidak Bisa Diperbaiki</option>
            </select>
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d') }}" class="form-input" style="margin-bottom:12px;">
            <label class="form-label">Biaya Perbaikan (opsional)</label>
            <input type="number" name="biaya_perbaikan" class="form-input" placeholder="0" style="margin-bottom:16px;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan</button>
        </form>
    </div>
</div>
@endif
@endforeach

@endsection
