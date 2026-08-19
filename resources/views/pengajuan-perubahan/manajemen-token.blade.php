@extends('layouts.app')
@section('title', 'Manajemen Token')
@section('page-title', 'Manajemen Token - Ajuan Perubahan')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Lihat token semua kelas sekaligus (biasanya wali kelas cuma lihat token kelasnya sendiri di menu Ajuan Perubahan).
</p>

<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-bottom:16px;">
    <p style="font-size:11px;color:#64748b;margin:0 0 4px;">Tautan Pengajuan (sama untuk semua kelas)</p>
    <input type="text" readonly value="{{ url("/{$npsn}/pengajuan") }}" class="form-input" style="font-size:12px;background:#fff;" onclick="this.select()">
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Wali Kelas</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Token</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($waliKelasList as $wk)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;color:#0f172a;font-weight:500;">{{ $wk->kelas_lengkap }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $wk->guru?->nama ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:14px;font-family:monospace;font-weight:700;color:#7c3aed;letter-spacing:1px;">{{ $wk->token_efektif }}</td>
                <td style="padding:10px 16px;text-align:right;">
                    <form action="{{ route('pengajuan-perubahan.manajemen-token.baru', $wk) }}" method="POST" onsubmit="return confirm('Buat token baru untuk kelas {{ $wk->kelas_lengkap }}? Token lama gak bisa dipakai lagi.')">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Token Baru</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data wali kelas untuk tahun ajaran aktif.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
