@extends('layouts.pengguna')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('header-actions')
    <a href="{{ route('user.create') }}" class="btn btn-primary"><i class="ti ti-user-plus"></i> Tambah User</a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span style="font-size:13px;font-weight:700;color:#0f172a;">Daftar User</span>
        <span style="font-size:12px;color:#94a3b8;">{{ $users->total() }} user</span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Nama</th>
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Email</th>
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Password</th>
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Role</th>
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Status</th>
                    <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:9px;background:{{ $user->role === 'admin' ? '#eff6ff' : '#f0fdf4' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="ti {{ $user->role === 'admin' ? 'ti-shield-check' : 'ti-user' }}" style="font-size:16px;color:{{ $user->role === 'admin' ? '#1d4ed8' : '#16a34a' }};"></i>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#111827;margin:0;">{{ $user->name }}</p>
                                @if($user->id === auth()->id())
                                    <p style="font-size:10px;color:#94a3b8;margin:0;">(Anda)</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;color:#374151;">{{ $user->email }}</td>
                    <td style="padding:12px 16px;">
                        @if($user->password_plain)
                        <span style="display:inline-flex;align-items:center;gap:6px;font-family:monospace;font-size:12px;color:#374151;">
                            <span class="pw-dots" id="pw-dots-{{ $user->id }}">••••••••</span>
                            <span class="pw-real" id="pw-real-{{ $user->id }}" style="display:none;">{{ $user->password_plain }}</span>
                            <button type="button" onclick="togglePw({{ $user->id }})" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
                                <i class="ti ti-eye" id="pw-icon-{{ $user->id }}"></i>
                            </button>
                        </span>
                        @else
                        <span style="font-size:11px;color:#cbd5e1;">-</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="background:{{ $user->role === 'admin' ? '#dbeafe' : '#dcfce7' }};color:{{ $user->role === 'admin' ? '#1e40af' : '#166534' }};padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($user->aktif)
                            <span class="badge badge-aktif">Aktif</span>
                        @else
                            <span class="badge badge-keluar">Nonaktif</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                            <a href="{{ route('user.edit', $user) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#eff6ff;color:#1d4ed8;text-decoration:none;" title="Edit">
                                <i class="ti ti-pencil" style="font-size:15px;"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('user.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#fef2f2;color:#dc2626;border:none;cursor:pointer;" title="Hapus">
                                    <i class="ti ti-trash" style="font-size:15px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $users->links() }}</div>
    @endif
</div>

<script>
    function togglePw(id) {
        const dots = document.getElementById('pw-dots-' + id);
        const real = document.getElementById('pw-real-' + id);
        const icon = document.getElementById('pw-icon-' + id);
        const showing = real.style.display !== 'none';
        dots.style.display = showing ? 'inline' : 'none';
        real.style.display = showing ? 'none' : 'inline';
        icon.className = showing ? 'ti ti-eye' : 'ti ti-eye-off';
    }
</script>

@endsection
