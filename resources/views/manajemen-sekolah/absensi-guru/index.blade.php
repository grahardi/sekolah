@extends('layouts.manajemen-sekolah')
@section('title', 'Absensi Guru')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Absensi Guru</h2>
    <a href="{{ route('manajemen-sekolah.absensi-guru.rekap') }}" class="btn btn-secondary"><i class="ti ti-list"></i> Rekap Absensi Guru</a>
</div>

<form method="GET" style="margin-bottom:16px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama guru..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

@if($guruList->count() > 0)
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Guru</th><th style="padding:10px;">Status Hari Ini</th><th style="padding:10px 18px;">Aksi</th>
        </tr></thead>
        <tbody>
            @foreach($guruList as $g)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $g->nama }}</td>
                <td style="padding:10px;">
                    @if($g->absenHariIni)<span class="badge" style="background:#fef3c7;color:#92400e;">{{ $g->absenHariIni->status }}</span>
                    @else<span class="badge badge-aktif">Hadir</span>@endif
                </td>
                <td style="padding:10px 18px;">
                    <form action="{{ route('manajemen-sekolah.absensi-guru.store', $g) }}" method="POST" style="display:flex;gap:6px;">
                        @csrf
                        <select name="status" class="form-input" style="font-size:12px;padding:6px 8px;width:110px;">
                            @foreach(['Sakit','Izin','Alpha','Dispensasi'] as $st)
                            <option value="{{ $st }}" {{ ($g->absenHariIni->status ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="keterangan" value="{{ $g->absenHariIni->keterangan ?? '' }}" placeholder="Keterangan" class="form-input" style="font-size:12px;padding:6px 8px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-device-floppy"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@elseif($cari)
<p style="text-align:center;color:#94a3b8;padding:30px;">Tidak ada guru ditemukan.</p>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Cari nama guru dulu di atas.</p>
@endif
@endsection
