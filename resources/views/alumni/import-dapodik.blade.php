@extends('layouts.app')
@section('title', 'Import Alumni dari Dapodik')
@section('page-title', 'Import Alumni dari Dapodik')

@section('header-actions')
    <a href="{{ route('alumni.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div class="card" style="padding:20px;max-width:600px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Upload file "Daftar Peserta Didik" langsung dari unduhan Dapodik (bukan template kita sendiri).
        Semua siswa di file ini akan tersimpan dengan status <strong>Lulus</strong> (alumni), terpisah dari siswa aktif.
    </p>

    <form action="{{ route('alumni.import-dapodik.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">File Excel Dapodik</label>
        <input type="file" name="file_dapodik" accept=".xlsx,.xls" required class="form-input" style="margin-bottom:16px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-file-import"></i> Import Sekarang</button>
    </form>
</div>

@endsection
