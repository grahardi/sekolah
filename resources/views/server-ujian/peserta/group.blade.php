@extends('layouts.server-ujian')
@section('title', 'Kelola Kelompok')
@section('page-title', 'Kelola Kelompok / Kelas')

@section('header-actions')
    <a href="{{ route('server-ujian.peserta.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali ke Peserta</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<div class="card" style="padding:16px;margin-bottom:16px;background:#eff6ff;border-color:#bfdbfe;">
    <p style="font-size:12px;color:#1e40af;margin:0;"><i class="ti ti-info-circle"></i> Kelompok/kelas ini otomatis dibuat saat Sinkron Siswa dari Buku Induk. Kelompok yang masih punya anggota tidak bisa dihapus - pindahkan/hapus anggotanya dulu lewat menu Peserta.</p>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Angkatan</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Kelompok</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Jumlah Anggota</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupList as $g)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;color:#64748b;">{{ $g->parent_name }}</td>
                <td style="padding:10px 16px;font-size:13px;font-weight:600;">
                    <form action="{{ route('server-ujian.group.update', $g->id) }}" method="POST" style="display:flex;gap:6px;align-items:center;">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $g->name }}" class="form-input" style="width:140px;padding:6px 10px;">
                        <button type="submit" class="btn btn-secondary btn-sm">Simpan</button>
                    </form>
                </td>
                <td style="padding:10px 16px;text-align:center;">
                    <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $g->jumlah_anggota > 0 ? '#eff6ff' : '#f1f5f9' }};color:{{ $g->jumlah_anggota > 0 ? '#1e40af' : '#94a3b8' }};">{{ $g->jumlah_anggota }} Peserta</span>
                </td>
                <td style="padding:10px 16px;text-align:right;">
                    <form action="{{ route('server-ujian.group.destroy', $g->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kelompok {{ addslashes($g->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;" {{ $g->jumlah_anggota > 0 ? 'disabled title="Masih ada anggota"' : '' }}>Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Belum ada kelompok tersinkron. Jalankan Sinkron Siswa dulu dari halaman Server Ujian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
