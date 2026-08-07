@extends('layouts.app')

@section('title', 'Server Ujian')
@section('page-title', 'Server Ujian (Extraordinary CBT)')

@section('content')

@if($instance)
    <div class="card" style="padding:24px;max-width:560px;">
        <div style="display:flex;align-items:center;justify-content:between;gap:10px;margin-bottom:18px;">
            <p style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">{{ $instance->nama }}</p>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;
                background:{{ $sedangJalan ? '#dcfce7' : '#f1f5f9' }};color:{{ $sedangJalan ? '#166534' : '#64748b' }};margin-left:10px;">
                <span style="width:7px;height:7px;border-radius:50%;background:{{ $sedangJalan ? '#16a34a' : '#94a3b8' }};"></span>
                {{ $sedangJalan ? 'Sedang Jalan' : 'Tidak Jalan' }}
            </span>
        </div>

        @php $port = $instance->bacaEnv('SERVER_PORT'); @endphp
        <p style="font-size:13px;color:#64748b;margin:0 0 20px;">
            Port: <strong style="color:#0f172a;">{{ $port ?? '-' }}</strong>
        </p>

        @if($sedangJalan)
        <div style="display:flex;gap:10px;margin-bottom:16px;">
            <a href="http://163.227.0.18:{{ $port }}/adm#/" target="_blank" class="btn btn-primary" style="flex:1;justify-content:center;">
                <i class="ti ti-external-link"></i> Buka Server Ujian
            </a>
            <form action="{{ route('server-ujian.stop', $instance) }}" method="POST" style="flex:1;">
                @csrf
                <button type="submit" class="btn" style="width:100%;justify-content:center;background:#fef2f2;color:#dc2626;" onclick="return confirm('Hentikan server ujian?')">
                    <i class="ti ti-player-stop"></i> Hentikan Server
                </button>
            </form>
        </div>

        @if($instance->admin_email_tersambung)
        <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;font-size:13px;">
            <p style="font-weight:600;color:#0f172a;margin:0 0 8px;"><i class="ti ti-key"></i> Login ke Server Ujian</p>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#64748b;">Email</span>
                <span style="font-family:monospace;font-weight:600;">{{ $instance->admin_email_tersambung }}</span>
            </div>
            <p style="font-size:11px;color:#94a3b8;margin:8px 0 0;">Gunakan <strong>password yang sama</strong> seperti login sekolah.co.id kamu.</p>
        </div>
        @endif

        <div style="border:1px dashed #cbd5e1;border-radius:10px;padding:14px 16px;margin-top:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <div>
                <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;"><i class="ti ti-device-mobile"></i> Aplikasi APK untuk Siswa</p>
                <p style="font-size:11.5px;color:#94a3b8;margin:2px 0 0;">Generate aplikasi ujian Android khusus sekolahmu.</p>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" disabled style="opacity:.5;cursor:not-allowed;white-space:nowrap;">
                <i class="ti ti-clock"></i> Segera Hadir
            </button>
        </div>

        @else
        <form action="{{ route('server-ujian.run', $instance) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
                <i class="ti ti-player-play"></i> Jalankan Server
            </button>
        </form>
        @endif
    </div>
@elseif($requestAktif)
    <div class="card" style="padding:24px;max-width:520px;text-align:center;">
        <i class="ti ti-clock" style="font-size:32px;color:#d97706;"></i>
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:14px 0 6px;">Permintaan Sedang Diproses</p>
        <p style="font-size:13px;color:#64748b;margin:0;">
            Permintaan Server Ujian kamu (diajukan {{ $requestAktif->created_at->locale('id')->translatedFormat('d F Y') }}) sedang ditinjau tim kami.
            Kami akan menghubungimu setelah server siap.
        </p>
    </div>
@else
    <div class="card" style="padding:24px;max-width:520px;text-align:center;">
        <i class="ti ti-server-off" style="font-size:32px;color:#94a3b8;"></i>
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:14px 0 6px;">Belum Ada Server Ujian</p>
        <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
            Sekolahmu belum terhubung ke Server Ujian (Extraordinary CBT). Ajukan permintaan, tim kami akan segera memprosesnya.
        </p>
        <form action="{{ route('server-ujian.request') }}" method="POST" style="text-align:left;">
            @csrf
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-input" rows="2" placeholder="mis. Butuh untuk PTS bulan depan" style="margin-bottom:12px;"></textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="ti ti-send"></i> Request Server Ujian
            </button>
        </form>
    </div>
@endif

@endsection
