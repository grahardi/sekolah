@extends('layouts.sarpras')
@section('title', 'Peminjaman Barang')
@section('page-title', 'Peminjaman Barang')

@section('header-actions')
    <button type="button" onclick="document.getElementById('modal-pinjam').style.display='flex'" class="btn btn-primary"><i class="ti ti-plus"></i> Catat Peminjaman</button>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<form method="GET" style="margin-bottom:16px;max-width:240px;">
    <select name="status" class="form-input" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="dipinjam" {{ ($filters['status'] ?? '') === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
        <option value="dikembalikan" {{ ($filters['status'] ?? '') === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
    </select>
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Barang</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Peminjam</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Tgl Pinjam</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Rencana Kembali</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $p)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $p->asset->nama_barang ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $p->peminjam_nama }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $p->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                <td style="padding:10px 16px;">
                    @if($p->status === 'dikembalikan')
                    <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Dikembalikan</span>
                    @elseif($p->terlambat)
                    <span style="background:#fef2f2;color:#991b1b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Terlambat</span>
                    @else
                    <span style="background:#dbeafe;color:#1e40af;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Dipinjam</span>
                    @endif
                </td>
                <td style="padding:10px 16px;text-align:right;">
                    @if($p->status === 'dipinjam')
                    <form action="{{ route('sarpras.peminjaman.kembalikan', $p) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Tandai Kembali</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada catatan peminjaman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $peminjaman->links() }}</div>

<div id="modal-pinjam" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Catat Peminjaman Baru</p>
            <button type="button" onclick="document.getElementById('modal-pinjam').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('sarpras.peminjaman.store') }}" method="POST">
            @csrf
            <label class="form-label">Barang</label>
            <select name="asset_id" class="form-input" required style="margin-bottom:12px;">
                <option value="">-- Pilih Barang --</option>
                @foreach($assetsTersedia as $a)<option value="{{ $a->id }}">{{ $a->nama_barang }} ({{ $a->kode_barang }})</option>@endforeach
            </select>
            <label class="form-label">Nama Peminjam</label>
            <input type="text" name="peminjam_nama" class="form-input" required style="margin-bottom:12px;">
            <label class="form-label">Kontak (opsional)</label>
            <input type="text" name="peminjam_kontak" class="form-input" style="margin-bottom:12px;">
            <label class="form-label">Keperluan (opsional)</label>
            <input type="text" name="keperluan" class="form-input" style="margin-bottom:12px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Tgl Pinjam</label>
                    <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Rencana Kembali</label>
                    <input type="date" name="tanggal_kembali_rencana" value="{{ date('Y-m-d', strtotime('+' . ($sekolah->sarpras_ambang_batas_pinjam_hari ?? 7) . ' days')) }}" class="form-input" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan</button>
        </form>
    </div>
</div>

@endsection
