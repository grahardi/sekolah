@extends('layouts.sarpras')
@section('title', 'Master Data')
@section('page-title', 'Master Data - Lokasi & Sumber Dana')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- Lokasi --}}
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Lokasi / Tempat</p>
            <button onclick="document.getElementById('modal-lokasi-baru').style.display='flex'" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i> Tambah</button>
        </div>
        <div class="card" style="padding:0;overflow:hidden;">
            @forelse($locations as $l)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f1f5f9;">
                <div>
                    <p style="font-size:13px;color:#0f172a;margin:0;font-weight:500;">{{ $l->name }}</p>
                    @if($l->keterangan)<p style="font-size:11px;color:#94a3b8;margin:1px 0 0;">{{ $l->keterangan }}</p>@endif
                    <p style="font-size:11px;color:#94a3b8;margin:1px 0 0;">{{ $l->assets_count }} barang</p>
                </div>
                <div style="display:flex;gap:6px;">
                    <button onclick="document.getElementById('modal-lokasi-{{ $l->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></button>
                    <form action="{{ route('sarpras.master-data.lokasi.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus lokasi {{ $l->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                </div>
            </div>

            <div id="modal-lokasi-{{ $l->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
                <div class="card" style="max-width:400px;width:100%;padding:22px;">
                    <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Edit Lokasi</p>
                    <form action="{{ route('sarpras.master-data.lokasi.update', $l) }}" method="POST">
                        @csrf @method('PUT')
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="name" class="form-input" required value="{{ $l->name }}">
                        </div>
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-input" rows="2">{{ $l->keterangan }}</textarea>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="document.getElementById('modal-lokasi-{{ $l->id }}').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <p style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada lokasi.</p>
            @endforelse
        </div>
    </div>

    {{-- Sumber Dana --}}
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Sumber Dana</p>
            <button onclick="document.getElementById('modal-dana-baru').style.display='flex'" class="btn btn-primary btn-sm"><i class="ti ti-plus"></i> Tambah</button>
        </div>
        <div class="card" style="padding:0;overflow:hidden;">
            @forelse($fundingSources as $f)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f1f5f9;">
                <div>
                    <p style="font-size:13px;color:#0f172a;margin:0;font-weight:500;">{{ $f->name }}</p>
                    @if($f->keterangan)<p style="font-size:11px;color:#94a3b8;margin:1px 0 0;">{{ $f->keterangan }}</p>@endif
                    <p style="font-size:11px;color:#94a3b8;margin:1px 0 0;">{{ $f->assets_count }} barang</p>
                </div>
                <div style="display:flex;gap:6px;">
                    <button onclick="document.getElementById('modal-dana-{{ $f->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></button>
                    <form action="{{ route('sarpras.master-data.dana.destroy', $f) }}" method="POST" onsubmit="return confirm('Hapus sumber dana {{ $f->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                </div>
            </div>

            <div id="modal-dana-{{ $f->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
                <div class="card" style="max-width:400px;width:100%;padding:22px;">
                    <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Edit Sumber Dana</p>
                    <form action="{{ route('sarpras.master-data.dana.update', $f) }}" method="POST">
                        @csrf @method('PUT')
                        <div style="margin-bottom:12px;">
                            <label class="form-label">Nama Sumber Dana</label>
                            <input type="text" name="name" class="form-input" required value="{{ $f->name }}">
                        </div>
                        <div style="margin-bottom:16px;">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-input" rows="2">{{ $f->keterangan }}</textarea>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="document.getElementById('modal-dana-{{ $f->id }}').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <p style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada sumber dana.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Modal Tambah Lokasi --}}
<div id="modal-lokasi-baru" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:400px;width:100%;padding:22px;">
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Tambah Lokasi</p>
        <form action="{{ route('sarpras.master-data.lokasi.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Lokasi</label>
                <input type="text" name="name" class="form-input" required placeholder="mis. Laboratorium Komputer">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-input" rows="2"></textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="button" onclick="document.getElementById('modal-lokasi-baru').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Sumber Dana --}}
<div id="modal-dana-baru" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:400px;width:100%;padding:22px;">
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Tambah Sumber Dana</p>
        <form action="{{ route('sarpras.master-data.dana.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Sumber Dana</label>
                <input type="text" name="name" class="form-input" required placeholder="mis. Bosda, Bos Pusat, Komite">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-input" rows="2"></textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="button" onclick="document.getElementById('modal-dana-baru').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection
