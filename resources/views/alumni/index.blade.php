@extends('layouts.alumni')
@section('title', 'Data Alumni')
@section('page-title', 'Data Alumni')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('alumni.create') }}" class="btn btn-primary"><i class="ti ti-user-plus"></i> Tambah Alumni</a>
    @endif
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama..." class="form-input" style="max-width:240px;">
    <select name="tahun_masuk" class="form-input" style="max-width:180px;" onchange="this.form.submit()">
        <option value="">Semua Tahun Masuk</option>
        @foreach($tahunList as $t)
        <option value="{{ $t }}" {{ ($filters['tahun_masuk'] ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary"><i class="ti ti-search"></i> Cari</button>
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">NISN</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Tahun Masuk</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Tahun Lulus</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">No. Ijazah</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni as $a)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">
                    <a href="{{ route('alumni.arsip.show', $a) }}" style="color:inherit;text-decoration:none;">{{ $a->nama_lengkap }}</a>
                </td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $a->nisn }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $a->tahun_masuk ?: '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $a->tahun_lulus ?: '-' }}</td>
                <td style="padding:10px 16px;font-size:12px;color:#475569;font-family:monospace;">{{ $a->no_ijazah ?: '-' }}</td>
                <td style="padding:10px 16px;text-align:right;">
                    <a href="{{ route('alumni.arsip.show', $a) }}" class="btn btn-secondary btn-sm"><i class="ti ti-folder"></i> Berkas</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data alumni.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $alumni->links() }}</div>

@endsection
