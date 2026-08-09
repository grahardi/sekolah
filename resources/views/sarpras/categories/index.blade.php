@extends('layouts.sarpras')
@section('title', 'Kategori Barang')
@section('page-title', 'Kategori Barang')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <p style="font-size:13px;color:#64748b;margin:0;max-width:520px;">Kelola kategori barang (bisa bertingkat, mis. Elektronik &rarr; Laptop/Proyektor). Kategori dipakai saat mendata barang.</p>
    <button onclick="document.getElementById('modal-tambah').style.display='flex'" class="btn btn-primary"><i class="ti ti-plus"></i> Tambah Kategori</button>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    @if($tree->isEmpty())
    <p style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada kategori. Klik "Tambah Kategori" untuk mulai.</p>
    @else
    <div style="padding:8px 0;">
        @foreach($tree as $kategori)
            @include('sarpras.categories._baris', ['kategori' => $kategori, 'level' => 0, 'iconOptions' => $iconOptions, 'allCategories' => $allCategories])
        @endforeach
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Tambah Kategori</p>
        <form action="{{ route('sarpras.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" class="form-input" required placeholder="mis. Laptop">
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Induk Kategori (opsional)</label>
                <select name="parent_id" class="form-input">
                    <option value="">-- Kategori Utama (tanpa induk) --</option>
                    @foreach($allCategories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Ikon (opsional)</label>
                <select name="icon" class="form-input">
                    <option value="">-- Tanpa Ikon --</option>
                    @foreach($iconOptions as $kelas => $label)
                    <option value="{{ $kelas }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="button" onclick="document.getElementById('modal-tambah').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection
