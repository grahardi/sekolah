@extends('layouts.manajemen-sekolah')
@section('title', 'Rekap Absensi Guru')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Rekap Absensi Guru</h2>
    <a href="{{ route('manajemen-sekolah.absensi-guru.index') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Isi Absensi Guru</a>
</div>

<form method="GET" style="margin-bottom:16px;max-width:260px;">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Guru</th><th style="padding:10px;">Status</th><th style="padding:10px;">Keterangan</th>
        </tr></thead>
        <tbody>
            @forelse($data as $d)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $d->guru->nama ?? '-' }}</td>
                <td style="padding:10px;"><span class="badge" style="background:#fef3c7;color:#92400e;">{{ $d->status }}</span></td>
                <td style="padding:10px;color:#64748b;">{{ $d->keterangan ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Semua guru hadir pada tanggal ini (tidak ada catatan).</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $data->links() }}
@endsection
