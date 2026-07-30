@extends('layouts.app')
@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password Saya')

@section('content')
<div style="max-width:440px;">
    <div class="card">
        <div style="padding:28px 28px 16px;text-align:center;">
            <div style="width:52px;height:52px;background:#eff6ff;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="ti ti-lock" style="font-size:24px;color:#1d4ed8;"></i>
            </div>
            <h2 style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 4px;">Ganti Password</h2>
            <p style="font-size:12px;color:#64748b;">Masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role_label }})</p>
        </div>

        <div style="padding:0 28px 28px;">
            @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:16px;"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="ti ti-circle-x"></i>
                <div><ul style="margin:0;padding-left:16px;font-size:12px;">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            </div>
            @endif

            <form action="{{ route('user.change-password.update') }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Password Saat Ini <span style="color:#ef4444">*</span></label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Password Baru <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password" class="form-input" required minlength="6">
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Minimal 6 karakter</p>
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru <span style="color:#ef4444">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px;">
                        <i class="ti ti-device-floppy"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
