@extends('layouts.manajemen-sekolah')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
<p style="font-size:12px;color:#94a3b8;margin:-10px 0 16px;">
    Data ini sama dengan menu Guru di E-Rapor - untuk tambah/edit data guru, buka menu <a href="/erapor/guru" style="color:#2563EB;">E-Rapor &rarr; Guru</a>.
</p>

<form method="GET" style="margin-bottom:18px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">NIP/NUPTK</th><th style="padding:10px;">Status</th>
        </tr></thead>
        <tbody>
            @forelse($guruList as $g)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $g->nama }}</td>
                <td style="padding:10px;font-family:monospace;color:#64748b;">{{ $g->nip_nuptk ?? '-' }}</td>
                <td style="padding:10px;">
                    @if($g->isDariKepegawaian())
                    <span class="badge" style="background:#eff6ff;color:#1E3A5F;">Kepegawaian</span>
                    @else
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Guru Bantu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada data guru.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $guruList->links() }}
@endsection
