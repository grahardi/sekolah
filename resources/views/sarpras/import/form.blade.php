@extends('layouts.sarpras')
@section('title', 'Import Barang')
@section('page-title', 'Import Barang dari Excel')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div class="card" style="padding:20px;max-width:600px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Import banyak barang sekaligus dari file Excel. Kolom Kategori & Lokasi akan otomatis dibuat kalau belum ada.
        Kalau Kode Barang dikosongkan, sistem generate otomatis.
    </p>

    <a href="{{ route('sarpras.import.template') }}" class="btn btn-secondary" style="margin-bottom:16px;"><i class="ti ti-download"></i> Download Template</a>

    <form action="{{ route('sarpras.import.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls" required class="form-input" style="margin-bottom:16px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-file-import"></i> Import Sekarang</button>
    </form>
</div>

@endsection
