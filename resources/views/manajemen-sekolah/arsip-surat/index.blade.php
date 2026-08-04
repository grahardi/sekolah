@extends('layouts.manajemen-sekolah')
@section('title', 'Arsip Surat')

@section('content')
<h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 16px;">Arsip Surat (Bukti Sakit/Izin)</h2>

<form method="GET" style="margin-bottom:16px;max-width:260px;">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
    @forelse($arsip as $a)
    <div class="card" style="overflow:hidden;">
        <a href="{{ asset('storage/'.$a->foto_bukti) }}" target="_blank">
            <img src="{{ asset('storage/'.$a->foto_bukti) }}" style="width:100%;height:160px;object-fit:cover;">
        </a>
        <div style="padding:12px 14px;">
            <p style="font-weight:700;color:#0f172a;margin:0 0 2px;font-size:13px;">{{ $a->siswa->nama_lengkap ?? '-' }}</p>
            <p style="font-size:11px;color:#64748b;margin:0;">{{ $a->status }} &middot; {{ $a->tanggal->locale('id')->translatedFormat('d M Y') }}</p>
        </div>
    </div>
    @empty
    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:30px;">Tidak ada arsip surat pada tanggal ini.</p>
    @endforelse
</div>
{{ $arsip->links() }}
@endsection
