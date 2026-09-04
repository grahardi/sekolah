@extends('layouts.app')
@section('title', $tiket->subjek)
@section('page-title', 'Tiket: ' . $tiket->subjek)

@section('header-actions')
    <a href="{{ route('tiket.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

@php $warna = $tiket->warnaBadge(); @endphp
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;">{{ $tiket->labelStatus() }}</span>
    <p style="font-size:12px;color:#94a3b8;margin:0;">Dibuat {{ $tiket->created_at->locale('id')->diffForHumans() }} oleh {{ $tiket->dibuatOleh->name }}</p>
</div>

<div class="card" style="padding:18px;margin-bottom:16px;max-height:520px;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">
    @foreach($tiket->pesan as $p)
    <div style="display:flex;{{ $p->dari_superadmin ? 'justify-content:flex-start;' : 'justify-content:flex-end;' }}">
        <div style="max-width:75%;background:{{ $p->dari_superadmin ? '#f1f5f9' : '#1E3A5F' }};color:{{ $p->dari_superadmin ? '#0f172a' : '#fff' }};padding:10px 14px;border-radius:12px;">
            <p style="font-size:10px;opacity:.7;margin:0 0 4px;font-weight:600;">{{ $p->dari_superadmin ? 'Admin sekolah.co.id' : ($p->user->name ?? 'Kamu') }}</p>
            <p style="font-size:13px;margin:0;white-space:pre-wrap;">{{ $p->pesan }}</p>
            <p style="font-size:9px;opacity:.6;margin:6px 0 0;">{{ $p->created_at->locale('id')->format('d M Y, H:i') }}</p>
        </div>
    </div>
    @endforeach
</div>

@if($tiket->status !== 'selesai')
<form action="{{ route('tiket.balas', $tiket) }}" method="POST">
    @csrf
    <div class="card" style="padding:14px;">
        <textarea name="pesan" rows="3" class="form-input" required placeholder="Tulis balasan..."></textarea>
        <div style="display:flex;justify-content:flex-end;margin-top:10px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-send"></i> Kirim Balasan</button>
        </div>
    </div>
</form>
@else
<div style="background:#f1f5f9;color:#64748b;padding:14px 16px;border-radius:8px;font-size:13px;">
    Tiket ini sudah ditandai selesai. Kalau masih ada kendala, kirim balasan untuk membuka kembali.
    <form action="{{ route('tiket.balas', $tiket) }}" method="POST" style="margin-top:10px;">
        @csrf
        <textarea name="pesan" rows="2" class="form-input" required placeholder="Tulis balasan untuk buka lagi tiket ini..."></textarea>
        <div style="display:flex;justify-content:flex-end;margin-top:10px;">
            <button type="submit" class="btn btn-secondary btn-sm">Kirim & Buka Lagi</button>
        </div>
    </form>
</div>
@endif

@endsection
