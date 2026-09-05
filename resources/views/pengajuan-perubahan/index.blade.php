@extends('layouts.app')
@section('title', 'Ajuan Perubahan Data')
@section('page-title', 'Ajuan Perubahan Data Siswa')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Siswa/orang tua bisa mengajukan perubahan data lewat tautan sekolah + token kelas. Kamu tinggal review & setujui perubahan yang masuk di sini.
</p>

@if($waliKelasSaya)
<div class="card" style="padding:18px;margin-bottom:20px;background:#eff6ff;border-color:#bfdbfe;">
    <p style="font-size:12px;font-weight:700;color:#1e40af;margin:0 0 10px;"><i class="ti ti-key"></i> Token Kelas {{ $waliKelasSaya->kelas_lengkap }} - Bagikan ke Siswa/Orang Tua</p>
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:220px;">
            <label style="font-size:11px;color:#64748b;">Tautan (sama untuk seluruh sekolah)</label>
            <input type="text" readonly value="{{ url("/{$npsn}/pengajuan") }}" class="form-input" style="font-size:12px;" onclick="this.select()">
        </div>
        <div style="min-width:140px;">
            <label style="font-size:11px;color:#64748b;">Token Kelas Ini</label>
            <input type="text" readonly value="{{ $waliKelasSaya->token_efektif }}" class="form-input" style="font-family:monospace;font-weight:700;font-size:16px;letter-spacing:2px;" onclick="this.select()">
        </div>
        <form action="{{ route('pengajuan-perubahan.token-baru') }}" method="POST" onsubmit="return confirm('Buat token baru? Token lama gak bisa dipakai lagi.')">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Token Baru</button>
        </form>
    </div>
</div>
@endif

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $s)
            @php
            $pengajuan = $s->pengajuanPerubahan;
            $status = $pengajuan?->status ?? 'belum_isi';
            $warna = ['belum_isi' => ['bg' => '#f1f5f9', 'txt' => '#64748b'], 'menunggu_approval' => ['bg' => '#fef9c3', 'txt' => '#854d0e'], 'sudah_approve' => ['bg' => '#dcfce7', 'txt' => '#166534'], 'tidak_ada_perubahan' => ['bg' => '#e0e7ff', 'txt' => '#3730a3']][$status];
            $label = ['belum_isi' => 'Belum Mengisi', 'menunggu_approval' => 'Menunggu Approval', 'sudah_approve' => 'Sudah Approve', 'tidak_ada_perubahan' => 'Tidak Ada Perubahan'][$status];
            @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;color:#0f172a;font-weight:500;">{{ $s->nama_lengkap }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $s->kelas }}{{ $s->rombel ? " - $s->rombel" : '' }}</td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $label }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    @if($status === 'menunggu_approval')
                    <a href="{{ route('pengajuan-perubahan.show', $s) }}" class="btn btn-primary btn-sm">Review</a>
                    @elseif($status === 'sudah_approve')
                    <a href="{{ route('pengajuan-perubahan.show', $s) }}" class="btn btn-secondary btn-sm" style="background:#e0e7ff;color:#3730a3;border-color:#c7d2fe;">Lihat</a>
                    @else
                    <span style="font-size:12px;color:#cbd5e1;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada siswa di kelas yang Anda wali-i.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
