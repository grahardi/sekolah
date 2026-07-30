@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('header-actions')
    <a href="{{ route('user.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="max-width:480px;">
    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;">Data User</span></div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="ti ti-circle-x"></i>
                <div>
                    <ul style="margin:0;padding-left:16px;font-size:12px;">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-input" required autofocus>
                    </div>
                    <div>
                        <label class="form-label">Email <span style="color:#ef4444">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                        <select name="role" class="form-input" required>
                            <option value="induk" {{ old('role') == 'induk' ? 'selected' : '' }}>Induk (Orang Tua / Wali — hanya lihat)</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (akses penuh)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Password <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password" class="form-input" required minlength="6">
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Minimal 6 karakter</p>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;"><i class="ti ti-device-floppy"></i> Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
