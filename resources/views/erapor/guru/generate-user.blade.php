@extends('layouts.erapor')
@section('title', 'Generate User Guru')
@section('page-title', 'Generate User Guru')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Buat akun login untuk semua guru sekaligus. Format email otomatis:
    <code>namadepan{{ auth()->user()->sekolah_id }}.urutguru@guru.sekolah.co.id</code> - untuk sementara
    akun guru cuma bisa akses modul E-Rapor (Kepegawaian & Buku Induk belum bisa).
</p>

@if(session('akun_massal_baru'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin-bottom:18px;">
    <p style="font-size:13px;font-weight:700;color:#166534;margin:0 0 10px;"><i class="ti ti-key"></i> {{ count(session('akun_massal_baru')) }} akun baru dibuat - catat sekarang, password tidak akan tampil lagi:</p>
    <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <thead><tr style="text-align:left;"><th style="padding:4px;">Nama</th><th style="padding:4px;">Email</th><th style="padding:4px;">Password</th></tr></thead>
        <tbody>
            @foreach(session('akun_massal_baru') as $a)
            <tr style="font-family:monospace;"><td style="padding:4px;">{{ $a['nama'] }}</td><td style="padding:4px;">{{ $a['email'] }}</td><td style="padding:4px;">{{ $a['password'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<form action="{{ route('erapor.guru.generate-user-massal') }}" method="POST" style="margin-bottom:18px;" onsubmit="return confirm('Generate akun untuk semua guru yang belum punya akun?')">
    @csrf
    <button type="submit" class="btn btn-primary"><i class="ti ti-users-plus"></i> Generate Akun untuk Semua Guru Baru</button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Urut</th><th style="padding:10px;">Nama</th><th style="padding:10px;">Status Akun</th><th style="padding:10px;">Email (kalau sudah ada)</th>
        </tr></thead>
        <tbody>
            @forelse($gurus as $g)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#94a3b8;">{{ $g->urutan }}</td>
                <td style="padding:10px;font-weight:700;">{{ $g->nama }}</td>
                <td style="padding:10px;">
                    @if($g->user_id)<span class="badge badge-aktif">Sudah Ada</span>@else<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Belum Ada</span>@endif
                </td>
                <td style="padding:10px;font-family:monospace;color:#64748b;">{{ $g->user?->email ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data guru.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
