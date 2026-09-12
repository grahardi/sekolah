@extends('layouts.server-ujian')
@section('title', 'Peserta Ujian')
@section('page-title', 'Peserta Ujian')

@section('header-actions')
    <a href="{{ route('server-ujian.group.index') }}" class="btn btn-secondary"><i class="ti ti-folder"></i> Kelola Kelompok</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px;">
    <div style="min-width:200px;">
        <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Cari Nama/No. Ujian</label>
        <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Ketik lalu Enter...">
    </div>
    <div style="min-width:180px;">
        <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Kelas / Kelompok</label>
        <select name="group_id" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($groupList as $g)
            <option value="{{ $g->id }}" {{ $filterGroup == $g->id ? 'selected' : '' }}>{{ $g->parent_name }} - {{ $g->name }}</option>
            @endforeach
        </select>
    </div>
    <div style="min-width:160px;">
        <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Agama</label>
        <select name="agama_id" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Agama</option>
            @foreach($agamaList as $a)
            <option value="{{ $a->id }}" {{ $filterAgama == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
            @endforeach
        </select>
    </div>
    <div style="min-width:160px;">
        <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Urutkan</label>
        <select name="sort" class="form-input" onchange="this.form.submit()">
            <option value="kelas" {{ $sort === 'kelas' ? 'selected' : '' }}>Kelas / Kelompok</option>
            <option value="agama" {{ $sort === 'agama' ? 'selected' : '' }}>Agama</option>
            <option value="nama" {{ $sort === 'nama' ? 'selected' : '' }}>Nama</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-search"></i> Terapkan</button>
    @if($filterGroup || $filterAgama || $search)
    <a href="{{ route('server-ujian.peserta.index') }}" class="btn btn-secondary btn-sm">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">No. Ujian</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Kelas / Kelompok</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Agama</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertaList as $p)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;">{{ $p->nama }}</td>
                <td style="padding:10px 16px;font-size:12px;color:#94a3b8;font-family:monospace;">{{ $p->no_ujian }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#334155;">{{ $p->group_parent_name ? "{$p->group_parent_name} - {$p->group_name}" : '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#334155;">{{ $p->agama_nama ?? '-' }}</td>
                <td style="padding:10px 16px;text-align:center;">
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $p->status == 1 ? '#dcfce7' : '#fef2f2' }};color:{{ $p->status == 1 ? '#166534' : '#991b1b' }};">{{ $p->status == 1 ? 'AKTIF' : 'TERBLOKIR' }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    <a href="{{ route('server-ujian.peserta.edit', $p->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form action="{{ route('server-ujian.peserta.destroy', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus peserta {{ addslashes($p->nama) }}? Data ujian terkait jg ikut hilang.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Tidak ada peserta yang cocok dengan filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($totalData > $perPage)
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;">
    <span style="font-size:13px;color:#64748b;">Halaman {{ $page }} dari {{ (int) ceil($totalData / $perPage) }} ({{ $totalData }} total peserta)</span>
    <div style="display:flex;gap:6px;">
        <a href="{{ request()->fullUrlWithQuery(['page' => max(1, $page - 1)]) }}" class="btn btn-secondary btn-sm">Sebelumnya</a>
        <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="btn btn-secondary btn-sm">Selanjutnya</a>
    </div>
</div>
@endif

@endsection
