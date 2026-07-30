@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User — ' . $user->name)

@section('header-actions')
    <a href="{{ route('user.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;">

    {{-- Data User --}}
    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;">Data User</span></div>
        <div class="card-body">
            @if($errors->any() && !$errors->has('password'))
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="ti ti-circle-x"></i>
                <div><ul style="margin:0;padding-left:16px;font-size:12px;">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            </div>
            @endif

            <form action="{{ route('user.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Email <span style="color:#ef4444">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                        <select name="role" class="form-input" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="induk" {{ old('role', $user->role) == 'induk' ? 'selected' : '' }}>Induk (hanya lihat)</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (akses penuh)</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Anda tidak bisa mengubah role akun sendiri.</p>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="aktif" id="aktif" value="1"
                               {{ old('aktif', $user->aktif) ? 'checked' : '' }}
                               {{ $user->id === auth()->id() ? 'disabled' : '' }}
                               style="width:16px;height:16px;">
                        <label for="aktif" style="font-size:13px;color:#374151;cursor:pointer;">Akun aktif (bisa login)</label>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="aktif" value="1">
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reset Password --}}
    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-key" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#d97706;"></i> Reset Password</span></div>
        <div class="card-body">
            @if($errors->has('password'))
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="ti ti-circle-x"></i> {{ $errors->first('password') }}
            </div>
            @endif

            <p style="font-size:12px;color:#64748b;margin-bottom:16px;">
                Gunakan ini untuk mengganti password user secara langsung tanpa perlu tahu password lama mereka (mis. jika lupa password).
            </p>

            <form action="{{ route('user.reset-password', $user) }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Password Baru <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password" class="form-input" required minlength="6">
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Minimal 6 karakter</p>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" required minlength="6">
                    </div>
                    <button type="submit" class="btn" style="width:100%;justify-content:center;background:#d97706;color:#fff;">
                        <i class="ti ti-key"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
