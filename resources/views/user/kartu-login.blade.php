@extends('layouts.pengguna')
@section('title', 'Kartu Login Guru')
@section('page-title', 'Kartu Login Guru')

@section('header-actions')
    <button onclick="window.print()" class="btn btn-primary no-print"><i class="ti ti-printer"></i> Cetak Semua</button>
@endsection

@section('content')

<style>
@media print {
    .no-print, .sidebar, .topbar, header, nav { display: none !important; }
    .main-content, body { padding: 0 !important; margin: 0 !important; }
    .grid-kartu { page-break-inside: auto; }
    .kartu-login { page-break-inside: avoid; }
}
.grid-kartu { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.kartu-login {
    border: 1.5px dashed #94a3b8; border-radius: 12px; padding: 18px;
    display: flex; gap: 14px; align-items: center; background: #fff;
}
</style>

<p class="no-print" style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Kartu ini cuma menampilkan guru yang <strong>passwordnya masih default</strong> (belum pernah diganti sendiri).
    Begitu guru login & ganti password, otomatis hilang dari daftar ini.
</p>

@if($guruList->isEmpty())
<div class="card no-print" style="padding:30px;text-align:center;">
    <p style="font-size:13px;color:#94a3b8;margin:0;">Tidak ada guru dengan password default saat ini - semua sudah pernah ganti password sendiri.</p>
</div>
@else
<div class="grid-kartu">
    @foreach($guruList as $guru)
    @php $urlLogin = url('/login'); @endphp
    <div class="kartu-login">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($urlLogin) }}" alt="QR Login" style="width:80px;height:80px;flex-shrink:0;">
        <div style="flex:1;">
            <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin:0 0 2px;">Kartu Login Aplikasi</p>
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 6px;">{{ $guru->name }}</p>
            <table style="font-size:11px;color:#374151;">
                <tr><td style="padding-right:8px;color:#94a3b8;">Website</td><td>: sekolah.co.id</td></tr>
                <tr><td style="padding-right:8px;color:#94a3b8;">Username</td><td>: {{ $guru->email }}</td></tr>
                <tr><td style="padding-right:8px;color:#94a3b8;">Password</td><td style="font-family:monospace;font-weight:700;">: {{ $guru->password_plain }}</td></tr>
            </table>
            <p style="font-size:9px;color:#cbd5e1;margin:6px 0 0;">Scan QR atau buka sekolah.co.id, lalu ganti password setelah login pertama.</p>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
