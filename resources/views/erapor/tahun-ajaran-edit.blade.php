@extends('layouts.erapor')
@section('title', 'Edit Tahun Ajaran')
@section('page-title', 'Edit Tahun Ajaran')

@section('header-actions')
    <a href="{{ route('erapor.tahun-ajaran') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.tahun-ajaran.update', $tahunAjaran) }}" method="POST" class="card" style="max-width:480px;margin:0 auto;padding:24px;">
    @csrf
    @method('PUT')
    <div style="margin-bottom:16px;">
        <label class="form-label">Nama (mis. 2025/2026)</label>
        <input name="nama" value="{{ $tahunAjaran->nama }}" class="form-input" required>
    </div>
    <div style="margin-bottom:20px;">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-input" required>
            <option value="Ganjil" {{ $tahunAjaran->semester === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="Genap" {{ $tahunAjaran->semester === 'Genap' ? 'selected' : '' }}>Genap</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
</form>
@endsection
