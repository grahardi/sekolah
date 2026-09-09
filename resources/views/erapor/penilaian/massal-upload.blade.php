@extends('layouts.erapor')
@section('title', 'Upload Nilai Massal')
@section('page-title', 'Upload Nilai Massal')

@section('header-actions')
    <a href="{{ route('erapor.penilaian.massal-form') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left"></i> Buat Penilaian Massal
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

@if(session('tidakKetemuSiswa') && count(session('tidakKetemuSiswa')) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;background:#fef2f2;border-color:#fecaca;max-width:600px;">
    <p style="font-size:13px;font-weight:700;color:#991b1b;margin:0 0 8px;">No. Induk Tidak Ketemu</p>
    @foreach(session('tidakKetemuSiswa') as $noInduk => $jumlah)
    <p style="font-size:12px;color:#7f1d1d;margin:2px 0;">{{ $noInduk }} - {{ $jumlah }} baris</p>
    @endforeach
</div>
@endif

@if(session('tidakKetemuPenilaian') && count(session('tidakKetemuPenilaian')) > 0)
<div class="card" style="padding:16px;margin-bottom:16px;background:#fffbeb;border-color:#fde68a;max-width:600px;">
    <p style="font-size:13px;font-weight:700;color:#92400e;margin:0 0 8px;">Kombinasi Kelas-Mapel Belum Ada Penilaiannya</p>
    <p style="font-size:11px;color:#78350f;margin:0 0 8px;">Baris ini dilewati - kemungkinan kelas/mapel siswa tsb belum dibuatkan penilaian PTS/PAS (buat dulu lewat menu "Buat Penilaian Massal").</p>
    @foreach(session('tidakKetemuPenilaian') as $label => $jumlah)
    <p style="font-size:12px;color:#78350f;margin:2px 0;">{{ $label }} - {{ $jumlah }} baris</p>
    @endforeach
</div>
@endif

<div class="card" style="padding:20px;max-width:560px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
        Upload SATU file untuk semua kelas & mapel sekaligus. Kolom wajib: <strong>no_induk, mapel, nilai</strong> (kolom nama & kelas opsional, cuma info). Siswa dicocokkan otomatis by No. Induk (NIS/NISN), kelasnya diambil dari data Buku Induk siswa tsb.
    </p>

    <form action="{{ route('erapor.penilaian.massal-template') }}" method="GET" style="margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid #f1f5f9;">
        <label class="form-label">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" class="form-input" required style="margin-bottom:12px;">
            <option value="">-- Pilih --</option>
            @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ ($prefill['tahun_ajaran_id'] ?? null) == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
            @endforeach
        </select>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <select name="semester" class="form-input" required>
                <option value="1" {{ ($prefill['semester'] ?? null) == '1' ? 'selected' : '' }}>Semester 1</option>
                <option value="2" {{ ($prefill['semester'] ?? null) == '2' ? 'selected' : '' }}>Semester 2</option>
            </select>
            <select name="subjenis_penilaian" class="form-input" required>
                <option value="Sumatif Tengah Semester" {{ ($prefill['subjenis_penilaian'] ?? null) == 'Sumatif Tengah Semester' ? 'selected' : '' }}>PTS</option>
                <option value="Sumatif Akhir Semester" {{ ($prefill['subjenis_penilaian'] ?? null) == 'Sumatif Akhir Semester' ? 'selected' : '' }}>PAS</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template (isi kolom nilai)</button>
    </form>

    <form action="{{ route('erapor.penilaian.massal-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" class="form-input" required style="margin-bottom:12px;">
            <option value="">-- Pilih --</option>
            @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ ($prefill['tahun_ajaran_id'] ?? null) == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
            @endforeach
        </select>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <select name="semester" class="form-input" required>
                <option value="1" {{ ($prefill['semester'] ?? null) == '1' ? 'selected' : '' }}>Semester 1</option>
                <option value="2" {{ ($prefill['semester'] ?? null) == '2' ? 'selected' : '' }}>Semester 2</option>
            </select>
            <select name="subjenis_penilaian" class="form-input" required>
                <option value="Sumatif Tengah Semester" {{ ($prefill['subjenis_penilaian'] ?? null) == 'Sumatif Tengah Semester' ? 'selected' : '' }}>PTS</option>
                <option value="Sumatif Akhir Semester" {{ ($prefill['subjenis_penilaian'] ?? null) == 'Sumatif Akhir Semester' ? 'selected' : '' }}>PAS</option>
            </select>
        </div>
        <label class="form-label">File Nilai (Excel/CSV)</label>
        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-input" style="margin-bottom:16px;">

        <button type="submit" class="btn btn-primary"><i class="ti ti-file-import"></i> Import Nilai</button>
    </form>
</div>

@endsection
