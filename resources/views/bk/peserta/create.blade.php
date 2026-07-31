@extends('layouts.bk')
@section('title', 'Buat Project Baru')
@section('page-title', 'Buat Project Baru Survey')

@section('header-actions')
    <a href="{{ route('bk.peserta.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('bk.peserta.store') }}" method="POST" class="card" style="max-width:640px;margin:0 auto;padding:24px;">
    @csrf
    <div style="margin-bottom:18px;">
        <label class="form-label">Pilih Survey <span style="color:#ef4444">*</span></label>
        <select name="survey_id" class="form-input" required>
            <option value="">-- Pilih survey --</option>
            @foreach($surveys as $s)
            <option value="{{ $s->id }}">{{ $s->judul }} ({{ ucfirst($s->status) }})</option>
            @endforeach
        </select>
        @error('survey_id')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="form-label">Pilih Kelas Target <span style="color:#ef4444">*</span></label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">
            @forelse($kelasList as $k)
            <label style="display:flex;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
                <input type="checkbox" name="target_kelas[]" value="{{ $k }}">
                {{ $k }}
            </label>
            @empty
            <p style="font-size:12px;color:#94a3b8;">Belum ada data kelas siswa aktif di Buku Induk.</p>
            @endforelse
        </div>
        @error('target_kelas')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:20px;">
        <i class="ti ti-device-floppy"></i> Simpan Project
    </button>
</form>
@endsection
