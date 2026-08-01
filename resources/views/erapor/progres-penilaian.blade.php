@extends('layouts.erapor')
@section('title', 'Progres Penilaian')
@section('page-title', 'Progres Penilaian - Kelas ' . $kelas . ($rombel ? " - $rombel" : ''))

@section('content')
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Siswa</th><th style="padding:10px;">Progres</th><th style="padding:10px;width:200px;"></th>
        </tr></thead>
        <tbody>
            @forelse($progres as $p)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $p['siswa']->nama_lengkap }}</td>
                <td style="padding:10px;color:#64748b;">{{ $p['sudah'] }} dari {{ $p['total'] }} mapel</td>
                <td style="padding:10px;">
                    <div style="background:#f1f5f9;border-radius:999px;height:8px;overflow:hidden;">
                        <div style="width:{{ $p['persen'] }}%;height:100%;background:{{ $p['persen'] == 100 ? '#16a34a' : ($p['persen'] >= 50 ? '#d97706' : '#dc2626') }};"></div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
