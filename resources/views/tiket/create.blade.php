@extends('layouts.app')
@section('title', 'Buat Tiket Baru')
@section('page-title', 'Buat Tiket Baru')

@section('header-actions')
    <a href="{{ route('tiket.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form action="{{ route('tiket.store') }}" method="POST">
    @csrf
    <div class="card" style="padding:20px;max-width:600px;">
        <div style="margin-bottom:14px;">
            <label class="form-label">Subjek</label>
            <input type="text" name="subjek" value="{{ old('subjek') }}" class="form-input" required placeholder="mis. Import Dapodik gagal terus">
        </div>
        <div>
            <label class="form-label">Pesan</label>
            <textarea name="pesan" rows="6" class="form-input" required placeholder="Jelaskan kendala/pertanyaan kamu selengkap mungkin...">{{ old('pesan') }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:16px;max-width:600px;">
        <a href="{{ route('tiket.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="ti ti-send"></i> Kirim Tiket</button>
    </div>
</form>

@endsection
