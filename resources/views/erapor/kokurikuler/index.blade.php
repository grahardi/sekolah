@extends('layouts.erapor')
@section('title', 'Kokurikuler (P5)')
@section('page-title', 'Kegiatan Kokurikuler (P5)')

@section('header-actions')
    <a href="{{ route('erapor.kokurikuler.pilih-asesmen') }}" class="btn btn-secondary"><i class="ti ti-clipboard-list"></i> Input Asesmen</a>
    <a href="{{ route('erapor.kokurikuler.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Rencanakan Kegiatan</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @forelse($kegiatans as $k)
    <div class="card" style="padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <p style="font-weight:800;color:#0f172a;margin:0 0 2px;font-size:15px;">{{ $k->nama_kegiatan }}</p>
                <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $k->tema }}</p>
            </div>
            <span class="badge" style="background:#eff6ff;color:#1E3A5F;">Sem {{ $k->semester == 1 ? 'Ganjil' : 'Genap' }}</span>
        </div>
        <p style="font-size:12px;color:#64748b;margin:10px 0 4px;">Bentuk: {{ $k->bentuk_kegiatan }}</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 10px;">Koordinator: {{ $k->koordinator->nama ?? '-' }}</p>
        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:14px;">
            @foreach($k->kelasTerlibats as $kt)<span class="badge" style="background:#f1f5f9;color:#374151;">{{ $kt->kelas_lengkap }}</span>@endforeach
        </div>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('erapor.kokurikuler.edit', $k) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center;"><i class="ti ti-pencil"></i> Edit</a>
            <form action="{{ route('erapor.kokurikuler.destroy', $k) }}" method="POST" style="flex:1;" onsubmit="return confirm('Hapus kegiatan ini beserta semua asesmennya?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-trash"></i> Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:30px;">Belum ada kegiatan kokurikuler. Klik "Rencanakan Kegiatan" untuk mulai.</p>
    @endforelse
</div>
@endsection
