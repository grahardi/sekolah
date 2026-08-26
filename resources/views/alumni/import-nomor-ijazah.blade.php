@extends('layouts.alumni')
@section('title', 'Import Nomor Ijazah')
@section('page-title', 'Import Nomor Ijazah')

@section('header-actions')
    <a href="{{ route('alumni.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div class="card" style="padding:20px;max-width:600px;margin-bottom:16px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Upload file export dari <strong>Manajemen Ijazah Dapodik</strong> / Penerbitan NIN.
        Formatnya 2 kolom: NISN dan Nomor Seri Ijazah. Dicocokkan otomatis by NISN ke data siswa yang sudah ada
        (siswa aktif maupun alumni, tidak dibuat data baru).
    </p>

    <form action="{{ route('alumni.import-nomor-ijazah.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">File Excel Penerbitan Ijazah</label>
        <input type="file" name="file_ijazah" accept=".xlsx,.xls" required class="form-input" style="margin-bottom:16px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-file-import"></i> Import Sekarang</button>
    </form>
</div>

@if(session('warnings_ijazah') && count(session('warnings_ijazah')) > 0)
<div class="card" style="padding:16px;max-width:600px;">
    <p style="font-size:12px;font-weight:700;color:#92400e;margin:0 0 8px;">Baris yang dilewati:</p>
    <div style="max-height:240px;overflow-y:auto;font-size:11px;color:#78716c;">
        @foreach(session('warnings_ijazah') as $w)
        <p style="margin:2px 0;">{{ $w }}</p>
        @endforeach
    </div>
</div>
@endif

@endsection
