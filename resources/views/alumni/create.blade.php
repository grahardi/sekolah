@extends('layouts.alumni')
@section('title', 'Tambah Alumni')
@section('page-title', 'Tambah Data Alumni Manual')

@section('header-actions')
    <a href="{{ route('alumni.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form action="{{ route('alumni.store') }}" method="POST">
    @csrf
    <div class="card" style="padding:20px;max-width:600px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="grid-column:span 2;">
                <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">NISN <span style="color:#ef4444">*</span></label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                <select name="jenis_kelamin" class="form-input" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Kelas Terakhir</label>
                <input type="text" name="kelas" value="{{ old('kelas', '9') }}" class="form-input" placeholder="mis. 9">
            </div>
            <div>
                <label class="form-label">Tahun Masuk</label>
                <input type="number" name="tahun_masuk" value="{{ old('tahun_masuk') }}" class="form-input" min="2000" max="{{ date('Y') }}">
            </div>
            <div>
                <label class="form-label">Tahun Lulus</label>
                <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', date('Y')) }}" class="form-input" min="2000" max="{{ date('Y') + 1 }}">
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:16px;max-width:600px;">
        <a href="{{ route('alumni.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="ti ti-device-floppy"></i> Simpan</button>
    </div>
</form>

@endsection
