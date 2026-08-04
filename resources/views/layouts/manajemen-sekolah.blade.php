<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Manajemen Sekolah')</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #f5f9ff; color: #1E293B; }

        .topbar-ms {
            background: linear-gradient(135deg,#1E3A5F,#2563EB);
            padding: 0 20px; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 30;
        }
        .topbar-ms .brand { display: flex; align-items: center; gap: 8px; color: #fff; font-weight: 700; font-size: 15px; font-family: 'Space Grotesk', sans-serif; }
        .topbar-ms .brand i { font-size: 18px; color: #FBBF24; }
        .topbar-ms nav { display: flex; align-items: center; gap: 4px; }
        .topbar-ms nav a, .topbar-ms nav button {
            display: flex; align-items: center; gap: 6px; color: rgba(255,255,255,.85);
            text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 12px;
            border-radius: 8px; background: none; border: none; cursor: pointer;
        }
        .topbar-ms nav a:hover, .topbar-ms nav button:hover { background: rgba(255,255,255,.12); color: #fff; }
        .topbar-ms nav a.active { background: rgba(255,255,255,.18); color: #fff; }
        .topbar-ms nav a i, .topbar-ms nav button i { font-size: 15px; }

        .page-body-ms { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; }
        .card-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; }
        .form-label { font-size: 12px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
        .form-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 13px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-sm { padding: 6px 10px; font-size: 12px; }
        .btn-primary { background: #2563EB; color: #fff; }
        .btn-secondary { background: #f1f5f9; color: #374151; }
        .btn-danger { background: #fef2f2; color: #dc2626; }
        .badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-aktif { background: #dcfce7; color: #166534; }
        .sb-demo-badge { background: #f1f5f9; color: #94a3b8; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: 700; }
        .sb-item-demo { opacity: .6; cursor: not-allowed; }

        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f0fdf4; color: #166534; }
        .alert-warning { background: #fffbeb; color: #92400e; }
        .alert-error { background: #fef2f2; color: #991b1b; }
    </style>
</head>
<body>

@php
    $isDemoReadonly = ! auth()->user()->isAdmin() && auth()->user()->sekolah?->is_demo;
@endphp

<div class="topbar-ms">
    <div class="brand"><i class="ti ti-building"></i> Manajemen Sekolah</div>
    <nav>
        <a href="/dashboard"><i class="ti ti-home"></i> Home</a>
        <a href="{{ route('manajemen-sekolah.dashboard') }}" class="{{ request()->routeIs('manajemen-sekolah.dashboard') ? 'active' : '' }}"><i class="ti ti-layout-dashboard"></i> Menu</a>
        <a href="{{ route('manajemen-sekolah.data-siswa') }}" class="{{ request()->routeIs('manajemen-sekolah.data-siswa') ? 'active' : '' }}"><i class="ti ti-users"></i> Siswa</a>
        <a href="{{ route('manajemen-sekolah.data-guru') }}" class="{{ request()->routeIs('manajemen-sekolah.data-guru') ? 'active' : '' }}"><i class="ti ti-user-check"></i> Guru</a>
        <form action="{{ route('manajemen-sekolah.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit"><i class="ti ti-logout"></i> Logout</button>
        </form>
    </nav>
</div>

<div class="page-body-ms">
    @if($isDemoReadonly)
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
        <i class="ti ti-eye" style="font-size:18px;color:#2563EB;flex-shrink:0;"></i>
        <p style="font-size:12.5px;color:#1e40af;margin:0;">
            <strong>Mode Demo</strong> - akun ini hanya untuk melihat data (read only). Fitur tambah/edit/hapus dinonaktifkan.
        </p>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> {{ session('warning') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error"><i class="ti ti-circle-x"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
