<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Buku Induk Siswa')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Icon font di-hosting sendiri di /public/vendor — tidak bergantung CDN luar --}}
    <link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar {
            width: 248px; background: #0f1f45;
            position: fixed; top:0; left:0; bottom:0;
            display: flex; flex-direction: column; z-index: 40;
        }
        .sb-brand {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 12px;
        }
        .sb-brand-icon {
            width: 38px; height: 38px; background: #1e40af;
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
        .sb-brand-icon i { font-size: 20px; color: #93c5fd; }
        .sb-brand-name { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.3; }
        .sb-brand-sub  { font-size: 11px; color: #64a0d4; margin-top: 1px; }

        .sb-nav { flex: 1; padding: 10px 10px; overflow-y: auto; }

        .sb-section {
            font-size: 10px; font-weight: 700; color: #4a7aa5;
            text-transform: uppercase; letter-spacing: .08em;
            padding: 12px 10px 5px;
        }

        .sb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: #94b8d8; font-size: 13px; font-weight: 500;
            text-decoration: none; transition: background .12s, color .12s;
            white-space: nowrap; margin-bottom: 1px;
        }
        .sb-item:hover { background: rgba(255,255,255,.07); color: #e2eeff; }
        .sb-item.active { background: #1e3a8a; color: #fff; }
        .sb-item i { font-size: 17px; flex-shrink: 0; width: 20px; text-align: center; }
        .sb-item.active i { color: #93c5fd; }

        .sb-divider { height: 1px; background: rgba(255,255,255,.07); margin: 6px 10px; }

        .sb-footer {
            padding: 10px 16px; border-top: 1px solid rgba(255,255,255,.08);
            font-size: 11px; color: #4a7aa5; text-align: center;
        }

        /* ── Topbar ──────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 30; background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 0 28px; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 0 #e9ecef;
        }
        .topbar-title { font-size: 15px; font-weight: 700; color: #0f172a; }

        /* ── Buttons ─────────────────────────────── */
        .btn { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; text-decoration:none; transition:all .12s; cursor:pointer; border:none; line-height:1; }
        .btn i { font-size:16px; }
        .btn-primary   { background:#1d4ed8; color:#fff; }
        .btn-primary:hover   { background:#1e40af; }
        .btn-secondary { background:#fff; color:#374151; border:1px solid #d1d5db; }
        .btn-secondary:hover { background:#f9fafb; }
        .btn-danger    { background:#ef4444; color:#fff; }
        .btn-danger:hover    { background:#dc2626; }
        .btn-success   { background:#16a34a; color:#fff; }
        .btn-success:hover   { background:#15803d; }
        .btn-sm { padding:5px 12px; font-size:12px; }
        .btn-sm i { font-size:14px; }

        /* ── Form ────────────────────────────────── */
        .form-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-input  { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px 12px; font-size:13px; background:#fff; outline:none; transition:border .12s, box-shadow .12s; color:#111827; }
        .form-input:focus { border-color:#1d4ed8; box-shadow:0 0 0 3px rgba(29,78,216,.12); }
        select.form-input { cursor:pointer; }
        textarea.form-input { resize:vertical; }

        /* ── Cards ───────────────────────────────── */
        .card { background:#fff; border-radius:12px; border:1px solid #e9ecef; }
        .card-header { padding:14px 18px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; }
        .card-body   { padding:18px; }

        /* ── Badges ──────────────────────────────── */
        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
        .badge-aktif  { background:#dcfce7; color:#166534; }
        .badge-lulus  { background:#dbeafe; color:#1e40af; }
        .badge-keluar { background:#fee2e2; color:#991b1b; }
        .badge-pindah { background:#fef9c3; color:#92400e; }

        /* ── Alert ───────────────────────────────── */
        .alert { display:flex; align-items:center; gap:10px; padding:11px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:18px; }
        .alert i { font-size:18px; flex-shrink:0; }
        .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .alert-warning { background:#fffbeb; border:1px solid #fde68a; color:#92400e; }
        .alert-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        /* ── Layout ──────────────────────────────── */
        .main-wrap { margin-left: 248px; min-height: 100vh; background: #f6f8fb; display:flex; flex-direction:column; }
        .page-body { padding: 24px 28px; flex: 1; }
    </style>
    @stack('styles')
</head>
<body style="background:#f6f8fb;">

{{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-icon"><i class="ti ti-notebook"></i></div>
        <div>
            <div class="sb-brand-name">Buku Induk</div>
            <div class="sb-brand-sub">Kurikulum Merdeka SMP</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">Utama</div>
        <a href="{{ route('siswa.index') }}"
           class="sb-item {{ request()->routeIs('siswa.index') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('siswa.create') }}"
           class="sb-item {{ request()->routeIs('siswa.create') ? 'active' : '' }}">
            <i class="ti ti-user-plus"></i><span>Tambah Siswa</span>
        </a>
        @endif

        <div class="sb-divider"></div>
        <div class="sb-section">Filter Cepat</div>
        <a href="{{ route('siswa.index', ['status'=>'aktif']) }}"
           class="sb-item {{ request()->query('status')==='aktif' ? 'active' : '' }}">
            <i class="ti ti-users"></i><span>Siswa Aktif</span>
        </a>
        <a href="{{ route('siswa.index', ['status'=>'lulus']) }}"
           class="sb-item {{ request()->query('status')==='lulus' ? 'active' : '' }}">
            <i class="ti ti-award"></i><span>Siswa Lulus</span>
        </a>
        <a href="{{ route('siswa.index', ['status'=>'pindah']) }}"
           class="sb-item {{ request()->query('status')==='pindah' ? 'active' : '' }}">
            <i class="ti ti-transfer"></i><span>Siswa Pindah</span>
        </a>

        @if(auth()->user()->isAdmin())
        <div class="sb-divider"></div>
        <div class="sb-section">Proses Siswa</div>
        <a href="{{ route('kenaikan.index') }}"
           class="sb-item {{ request()->routeIs('kenaikan.*') ? 'active' : '' }}">
            <i class="ti ti-arrow-up-circle"></i><span>Naik Kelas / Lulus</span>
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="sb-divider"></div>
        <div class="sb-section">Import / Export</div>
        <a href="{{ route('siswa.import.form') }}"
           class="sb-item {{ request()->routeIs('siswa.import.*') ? 'active' : '' }}">
            <i class="ti ti-file-import"></i><span>Import Data Siswa</span>
        </a>
        <a href="{{ route('nilai.import-massal') }}"
           class="sb-item {{ request()->routeIs('nilai.import-massal*') ? 'active' : '' }}">
            <i class="ti ti-table-import"></i><span>Import Nilai Massal</span>
        </a>
        <a href="{{ route('siswa.export.excel') }}" class="sb-item">
            <i class="ti ti-table-export"></i><span>Export Excel</span>
        </a>
        <a href="{{ route('siswa.export.pdf') }}" class="sb-item">
            <i class="ti ti-file-type-pdf"></i><span>Export PDF Daftar</span>
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="sb-divider"></div>
        <div class="sb-section">Pengaturan</div>
        <a href="{{ route('user.index') }}"
           class="sb-item {{ request()->routeIs('user.index') || request()->routeIs('user.create') || request()->routeIs('user.edit') ? 'active' : '' }}">
            <i class="ti ti-users-group"></i><span>Manajemen User</span>
        </a>
        @endif

        <div class="sb-divider"></div>
        <div class="sb-section">Akun Saya</div>
        <a href="{{ route('user.change-password') }}"
           class="sb-item {{ request()->routeIs('user.change-password') ? 'active' : '' }}">
            <i class="ti ti-key"></i><span>Ganti Password</span>
        </a>
    </nav>

    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.08);">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-user" style="font-size:16px;color:#93c5fd;"></i>
            </div>
            <div style="min-width:0;">
                <p style="font-size:12px;font-weight:700;color:#fff;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                <p style="font-size:10px;color:#64a0d4;margin:0;">{{ auth()->user()->role_label }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:7px;border-radius:7px;background:rgba(239,68,68,.15);color:#fca5a5;border:none;font-size:12px;font-weight:600;cursor:pointer;">
                <i class="ti ti-logout" style="font-size:14px;"></i> Keluar
            </button>
        </form>
    </div>
    <div class="sb-footer">v3.0 &nbsp;·&nbsp; {{ date('Y') }}</div>
</aside>

{{-- ── Main ─────────────────────────────────────────────────────────────── --}}
<div class="main-wrap">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title','Buku Induk Siswa')</span>
        <div style="display:flex;align-items:center;gap:10px;">
            @yield('header-actions')
        </div>
    </header>

    <div class="page-body">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="ti ti-circle-check"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning">
            <i class="ti ti-alert-triangle"></i> {{ session('warning') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">
            <i class="ti ti-circle-x"></i> {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
