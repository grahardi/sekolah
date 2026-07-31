<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Buku Induk Siswa')</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    {{-- Icon font di-hosting sendiri di /public/vendor — tidak bergantung CDN luar --}}
    <link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        /* ── Sidebar (disamakan dgn warna portal utama: biru #2563EB / kuning #FBBF24) ── */
        .sidebar {
            width: 248px; background: #2563EB;
            position: fixed; top:0; left:0; bottom:0;
            display: flex; flex-direction: column; z-index: 40;
            overflow-y: auto;
        }
        .sb-brand {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 12px;
        }
        .sb-brand-icon {
            width: 38px; height: 38px; background: rgba(255,255,255,.12);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
        .sb-brand-icon i { font-size: 20px; color: #FBBF24; }
        .sb-brand-name { font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: #fff; line-height: 1.3; }
        .sb-brand-sub  { font-size: 11px; color: rgba(255,255,255,.65); margin-top: 1px; }

        .sb-back { margin: 10px 10px 0; }
        .sb-back a {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 8px;
            color: rgba(255,255,255,.8); font-size: 12px; font-weight: 600;
            text-decoration: none; background: rgba(255,255,255,.08);
        }
        .sb-back a:hover { background: rgba(255,255,255,.14); color: #fff; }

        .sb-nav { padding: 10px 10px; }

        .sb-section {
            font-size: 10px; font-weight: 700; color: rgba(255,255,255,.55);
            text-transform: uppercase; letter-spacing: .08em;
            padding: 12px 10px 5px;
        }

        .sb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: rgba(255,255,255,.85); font-size: 13px; font-weight: 500;
            text-decoration: none; transition: background .12s, color .12s;
            white-space: nowrap; margin-bottom: 1px;
        }
        .sb-item:hover { background: rgba(255,255,255,.1); color: #fff; }
        .sb-item.active { background: #fff; color: #2563EB; font-weight: 600; }
        .sb-item i { font-size: 17px; flex-shrink: 0; width: 20px; text-align: center; }
        .sb-item.active i { color: #2563EB; }

        .sb-divider { height: 1px; background: rgba(255,255,255,.1); margin: 6px 10px; }

        .sb-item-demo {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: rgba(255,255,255,.4); font-size: 13px; font-weight: 500;
            white-space: nowrap; margin-bottom: 1px; cursor: not-allowed;
        }
        .sb-item-demo i { font-size: 17px; flex-shrink: 0; width: 20px; text-align: center; }
        .sb-demo-badge {
            margin-left: auto; font-size: 9px; font-weight: 700; text-transform: uppercase;
            background: rgba(255,255,255,.12); color: rgba(255,255,255,.6);
            padding: 2px 6px; border-radius: 999px; letter-spacing: .03em;
        }

        .sb-footer {
            padding: 10px 16px; border-top: 1px solid rgba(255,255,255,.08);
            font-size: 11px; color: rgba(255,255,255,.5); text-align: center;
        }

        /* ── Topbar ──────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 30; background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 0 28px; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 0 #e9ecef;
        }
        .topbar-title { font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #1E293B; }

        /* ── Buttons ─────────────────────────────── */
        .btn { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; text-decoration:none; transition:all .12s; cursor:pointer; border:none; line-height:1; }
        .btn i { font-size:16px; }
        .btn-primary   { background:#2563EB; color:#fff; }
        .btn-primary:hover   { background:#1d4ed8; }
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
        .form-input:focus { border-color:#2563EB; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
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
        .main-wrap { margin-left: 248px; min-height: 100vh; background: #F5F9FF; display:flex; flex-direction:column; }
        .page-body { padding: 24px 28px; flex: 1; }
    </style>
    @stack('styles')
</head>
<body style="background:#F5F9FF;">
@php
    // Akun read-only (role 'induk') di sekolah demo tetap melihat menu
    // import/export/pengaturan, tapi dalam kondisi non-aktif dgn keterangan -
    // supaya calon pengguna paham fitur itu ADA, cuma dimatikan buat demo.
    // Untuk sekolah non-demo, non-admin tetap tidak melihat menu itu sama sekali.
    $isDemoReadonly = ! auth()->user()->isAdmin() && auth()->user()->sekolah?->is_demo;
@endphp

{{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-icon"><i class="ti ti-notebook"></i></div>
        <div>
            <div class="sb-brand-name">sekolah.co.id</div>
            <div class="sb-brand-sub">Program BK</div>
        </div>
    </div>

    <div class="sb-back">
        <a href="/dashboard">
            <i class="ti ti-arrow-left" style="font-size:14px;"></i><span>Kembali ke Portal</span>
        </a>
    </div>

    <nav class="sb-nav">
        <div class="sb-section">Utama</div>
        <a href="{{ route('bk.survey.index') }}"
           class="sb-item {{ request()->routeIs('bk.survey.*') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i><span>Dashboard</span>
        </a>

        <div class="sb-divider"></div>
        <div class="sb-section">Survey / Asesmen</div>
        <a href="{{ route('bk.survey.index') }}"
           class="sb-item {{ request()->routeIs('bk.survey.index') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i><span>Semua Survey</span>
        </a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('bk.survey.create') }}"
           class="sb-item {{ request()->routeIs('bk.survey.create') ? 'active' : '' }}">
            <i class="ti ti-square-plus"></i><span>Buat Survey Baru</span>
        </a>
        @endif

        <div class="sb-divider"></div>
        <div class="sb-section">Konseling</div>
        <div class="sb-item-demo" title="Segera hadir">
            <i class="ti ti-notes"></i><span>Catatan Konseling</span><span class="sb-demo-badge">Segera</span>
        </div>
        <div class="sb-item-demo" title="Segera hadir">
            <i class="ti ti-alert-octagon"></i><span>Poin Pelanggaran</span><span class="sb-demo-badge">Segera</span>
        </div>
        <div class="sb-item-demo" title="Segera hadir">
            <i class="ti ti-home-2"></i><span>Kunjungan Rumah</span><span class="sb-demo-badge">Segera</span>
        </div>
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
        @if($isDemoReadonly)
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
            <i class="ti ti-eye" style="font-size:18px;color:#2563EB;flex-shrink:0;"></i>
            <p style="font-size:12.5px;color:#1e40af;margin:0;">
                <strong>Mode Demo</strong> - akun ini hanya untuk melihat data (read only). Fitur tambah/edit/hapus/import/export
                dinonaktifkan dan ditandai <span class="sb-demo-badge" style="color:#1e40af;background:#dbeafe;">Demo</span> di sidebar.
            </p>
        </div>
        @endif

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
