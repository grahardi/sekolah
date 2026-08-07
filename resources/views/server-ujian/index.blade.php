@extends('layouts.app')

@section('title', 'Server Ujian')
@section('page-title', 'Server Ujian')

@section('content')

<p style="font-size:13px;color:#64748b;margin:-8px 0 20px;max-width:600px;">
    Kelola server Extraordinary CBT (Server Ujian) khusus sekolahmu — nyalakan/matikan sesuai kebutuhan ujian.
</p>

@if($instance)
    @php $port = $instance->bacaEnv('SERVER_PORT'); @endphp
    <div class="card" style="max-width:600px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);padding:24px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti ti-devices-pc" style="font-size:26px;color:#fff;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:17px;font-weight:800;color:#fff;margin:0;">{{ $instance->nama }}</p>
                    <p style="font-size:12px;color:rgba(255,255,255,.7);margin:2px 0 0;">Extraordinary CBT &middot; Port {{ $port ?? '-' }}</p>
                </div>
                <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;flex-shrink:0;
                    background:{{ $sedangJalan ? '#22c55e' : 'rgba(255,255,255,.15)' }};color:#fff;">
                    <span style="width:7px;height:7px;border-radius:50%;background:#fff;{{ $sedangJalan ? 'box-shadow:0 0 0 3px rgba(255,255,255,.3);' : 'opacity:.5;' }}"></span>
                    {{ $sedangJalan ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <div style="padding:24px;">
            @if($sedangJalan)
            <div style="display:flex;gap:10px;margin-bottom:16px;">
                <a href="http://163.227.0.18:{{ $port }}/adm#/" target="_blank" class="btn btn-primary" style="flex:1;justify-content:center;padding:11px;">
                    <i class="ti ti-external-link"></i> Buka Server Ujian
                </a>
                <form action="{{ route('server-ujian.stop', $instance) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn" style="justify-content:center;background:#fef2f2;color:#dc2626;padding:11px 16px;" onclick="return confirm('Hentikan server ujian?')">
                        <i class="ti ti-player-stop"></i> Hentikan
                    </button>
                </form>
            </div>

            @if($instance->admin_email_tersambung)
            <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <i class="ti ti-shield-check" style="font-size:22px;color:#16a34a;flex-shrink:0;"></i>
                <p style="font-size:13px;color:#166534;margin:0;font-weight:600;">Silahkan Login dengan User dan Password Portal di Server Ujian</p>
            </div>
            @endif

            <form action="{{ route('server-ujian.sinkron-siswa', $instance) }}" method="POST" onsubmit="return confirm('Sinkron data siswa aktif ke Server Ujian? Ini akan buat/update grup kelas dan akun peserta.')">
                @csrf
                <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;padding:10px;">
                    <i class="ti ti-refresh"></i> Sinkron Data Siswa
                </button>
            </form>
            @else
            <form action="{{ route('server-ujian.run', $instance) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-bottom:16px;">
                    <i class="ti ti-player-play"></i> Jalankan Server
                </button>
            </form>
            @endif

            <div style="border:1px dashed #cbd5e1;border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:10px;background:#fce7f3;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ti ti-device-mobile" style="font-size:18px;color:#db2777;"></i>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;">Aplikasi APK untuk Siswa</p>
                        <p style="font-size:11.5px;color:#94a3b8;margin:1px 0 0;">Generate aplikasi ujian Android khusus sekolahmu.</p>
                    </div>
                </div>
                <span class="badge" style="background:#f1f5f9;color:#94a3b8;flex-shrink:0;">Segera Hadir</span>
            </div>
        </div>
    </div>
@elseif($requestAktif)
    <div class="card" style="padding:32px;max-width:480px;text-align:center;">
        <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="ti ti-clock" style="font-size:26px;color:#d97706;"></i>
        </div>
        <p style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px;">Permintaan Sedang Diproses</p>
        <p style="font-size:13px;color:#64748b;margin:0;">
            Diajukan {{ $requestAktif->created_at->locale('id')->translatedFormat('d F Y') }} - sedang ditinjau tim kami.
            Kami akan menghubungimu setelah server siap.
        </p>
    </div>
@else
    <div class="card" style="padding:32px;max-width:480px;text-align:center;">
        <div style="width:56px;height:56px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="ti ti-server-off" style="font-size:26px;color:#94a3b8;"></i>
        </div>
        <p style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 6px;">Belum Ada Server Ujian</p>
        <p style="font-size:13px;color:#64748b;margin:0 0 20px;">
            Sekolahmu belum terhubung ke Server Ujian (Extraordinary CBT). Ajukan permintaan, tim kami akan segera memprosesnya.
        </p>
        <form action="{{ route('server-ujian.request') }}" method="POST" style="text-align:left;">
            @csrf
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-input" rows="2" placeholder="mis. Butuh untuk PTS bulan depan" style="margin-bottom:12px;"></textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
                <i class="ti ti-send"></i> Request Server Ujian
            </button>
        </form>
    </div>
@endif

@endsection
