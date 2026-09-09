@extends('layouts.erapor')
@section('title', 'Penilaian Massal')
@section('page-title', 'Penilaian Massal')

@section('header-actions')
    <a href="{{ route('erapor.penilaian.massal-upload-form') }}" class="btn btn-secondary">
        <i class="ti ti-file-import"></i> Upload Nilai Massal
    </a>
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

<div class="card" style="padding:20px;max-width:560px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
        Buat penilaian PTS/PAS untuk <strong>semua kelas & mata pelajaran yang sudah ada pengajarnya</strong> sekaligus - gak perlu buat satu-satu per kelas. Tidak butuh Tujuan Pembelajaran (beda dari Sumatif TP biasa). Kelas/mapel yang sudah punya penilaian jenis ini di semester yang sama otomatis dilewati (gak dobel).
    </p>

    <form action="{{ route('erapor.penilaian.massal-store') }}" method="POST">
        @csrf

        <label class="form-label">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" class="form-input" required style="margin-bottom:14px;">
            <option value="">-- Pilih --</option>
            @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
            @endforeach
        </select>

        <label class="form-label">Semester</label>
        <select name="semester" class="form-input" required style="margin-bottom:14px;">
            <option value="1">Semester 1 (Ganjil)</option>
            <option value="2">Semester 2 (Genap)</option>
        </select>

        <label class="form-label">Jenis Penilaian</label>
        <select name="subjenis_penilaian" class="form-input" required style="margin-bottom:14px;">
            <option value="Sumatif Tengah Semester">Penilaian Tengah Semester (PTS)</option>
            <option value="Sumatif Akhir Semester">Penilaian Semester Akhir (PAS)</option>
        </select>

        <label class="form-label">Nama Penilaian</label>
        <input type="text" name="nama_penilaian" class="form-input" required placeholder="mis. PTS Ganjil 2026/2027" style="margin-bottom:14px;">

        <label class="form-label">Bobot Penilaian (%)</label>
        <input type="number" name="bobot_penilaian" class="form-input" required min="1" max="100" value="30" style="margin-bottom:14px;">

        <label class="form-label">Tanggal Penilaian (opsional)</label>
        <input type="date" name="tanggal_penilaian" class="form-input" style="margin-bottom:18px;">

        <button type="submit" class="btn btn-primary"><i class="ti ti-stack-2"></i> Buat untuk Semua Kelas & Mapel</button>
    </form>
</div>

@endsection
