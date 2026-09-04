@extends('layouts.app')
@section('title', 'Tiket Dukungan')
@section('page-title', 'Tiket Dukungan')

@section('header-actions')
    <a href="{{ route('tiket.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Buat Tiket Baru</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Ada kendala teknis atau pertanyaan soal sistem? Kirim tiket ke admin sekolah.co.id, kami akan balas di sini.
</p>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Subjek</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Terakhir Dibalas</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tiketList as $t)
            @php $warna = $t->warnaBadge(); @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;">
                    <a href="{{ route('tiket.show', $t) }}" style="color:#0f172a;font-weight:600;text-decoration:none;">
                        {{ $t->subjek }}
                        @if($t->ada_balasan_belum_dibaca_sekolah)
                        <span style="background:#ef4444;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:10px;margin-left:6px;">BARU</span>
                        @endif
                    </a>
                </td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $t->labelStatus() }}</span>
                </td>
                <td style="padding:10px 16px;font-size:12px;color:#94a3b8;">{{ $t->dibalas_terakhir_at?->locale('id')->diffForHumans() ?? '-' }}</td>
                <td style="padding:10px 16px;text-align:right;">
                    <a href="{{ route('tiket.show', $t) }}" class="btn btn-secondary btn-sm">Buka</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada tiket. Klik "Buat Tiket Baru" kalau butuh bantuan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
