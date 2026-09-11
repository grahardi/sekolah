@extends('layouts.server-ujian')
@section('title', 'Panel Pengawas Ujian')
@section('page-title', 'Panel Pengawas Ujian')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errorSkema)
<div style="background:#fffbeb;color:#92400e;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;"><i class="ti ti-alert-triangle"></i> {{ $errorSkema }}</div>
@endif

<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('server-ujian.panel-pengawas', ['view' => 'active']) }}" class="btn {{ $view === 'active' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Daftar Terblokir</a>
    <a href="{{ route('server-ujian.panel-pengawas', ['view' => 'history']) }}" class="btn {{ $view === 'history' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Riwayat Log</a>
    <a href="{{ route('server-ujian.panel-pengawas', ['view' => 'top_blocked']) }}" class="btn {{ $view === 'top_blocked' ? 'btn-primary' : 'btn-secondary' }} btn-sm">Maksimal Terblokir</a>
    <a href="{{ route('server-ujian.monitoring-ruangan') }}" class="btn btn-secondary btn-sm">Monitoring Ruangan</a>
</div>

<div class="card" style="padding:20px;margin-bottom:20px;{{ $isExpired ? 'background:#fff7ed;border-color:#fed7aa;' : '' }}">
    @if($tokenAktif)
    <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center;justify-content:space-between;">
        <div>
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:{{ $isExpired ? '#c2410c' : '#94a3b8' }};">Token {{ $isExpired ? 'Sudah Kadaluarsa' : 'Aktif Sekarang' }}</span>
            <p style="font-size:42px;font-weight:900;letter-spacing:.2em;margin:4px 0 0;color:{{ $isExpired ? '#f97316' : '#1d4ed8' }};">{{ $tokenAktif->token }}</p>
        </div>
        <div style="display:flex;gap:24px;">
            <div><span style="font-size:11px;color:#94a3b8;display:block;">Dibuat (WIB)</span><span style="font-family:monospace;font-weight:700;">{{ $createdAtWIB->format('d M Y H:i') }}</span></div>
            <div><span style="font-size:11px;color:#f87171;display:block;">Berakhir (WIB)</span><span style="font-family:monospace;font-weight:700;color:#dc2626;">{{ $expiredAtWIB->format('d M Y H:i') }}</span></div>
        </div>
    </div>
    @else
    <p style="color:#94a3b8;font-style:italic;margin:0;">Tidak ada token aktif.</p>
    @endif
</div>

@if($view === 'active')
<div class="card" style="padding:0;overflow:hidden;">
    <div style="padding:16px 18px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f1f5f9;">
        <p style="font-weight:700;color:#334155;margin:0;">Peserta Terblokir (Saat Ini)</p>
        <span style="background:#fef2f2;color:#dc2626;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">{{ count($pesertas) }} Peserta</span>
    </div>
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Peserta</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Blokir Ke-</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Alasan</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Waktu Blokir</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertas as $row)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;">{{ $row->nama }}</td>
                <td style="padding:10px 16px;text-align:center;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;font-size:11px;font-weight:700;background:{{ $row->total_blokir > 0 ? '#fed7aa' : '#f1f5f9' }};color:{{ $row->total_blokir > 0 ? '#c2410c' : '#64748b' }};">{{ (int)$row->total_blokir + 1 }}</span>
                </td>
                <td style="padding:10px 16px;font-size:13px;color:#64748b;font-style:italic;">{{ $row->block_reason ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#94a3b8;font-family:monospace;">
                    @if($row->blocked_at)
                    {{ (new DateTime($row->blocked_at, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('H:i:s') }}
                    @else - @endif
                </td>
                <td style="padding:10px 16px;text-align:center;">
                    <form method="POST" action="{{ route('server-ujian.panel-pengawas.aktifkan') }}" onsubmit="return confirm('Aktifkan akses untuk {{ addslashes($row->nama) }}?');">
                        @csrf
                        <input type="hidden" name="nama" value="{{ $row->nama }}">
                        <button type="submit" class="btn btn-primary btn-sm" style="background:#16a34a;">Aktifkan</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Tidak ada data peserta terblokir.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@elseif($view === 'history')
<div class="card" style="padding:0;overflow:hidden;">
    <div style="padding:16px 18px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f1f5f9;">
        <p style="font-weight:700;color:#334155;margin:0;">Riwayat Pengaktifan Blokir</p>
        <span style="background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">Total: {{ $totalData }} Rekaman</span>
    </div>
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Peserta</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Alasan Terakhir</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Waktu Blokir</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Waktu Diaktifkan</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Total Blokir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $log)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;">{{ $log->nama }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#64748b;">{{ $log->alasan_blokir ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#94a3b8;font-family:monospace;">
                    @if($log->jam_terblokir){{ (new DateTime($log->jam_terblokir, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d/m H:i:s') }}@else - @endif
                </td>
                <td style="padding:10px 16px;font-size:13px;color:#16a34a;font-weight:700;font-family:monospace;">{{ (new DateTime($log->jam_diaktifkan))->format('d/m H:i:s') }}</td>
                <td style="padding:10px 16px;text-align:center;"><span style="background:#1e293b;color:#fff;font-size:11px;font-weight:800;padding:3px 10px;border-radius:6px;">{{ $log->total_blokir }}x</span></td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Belum ada riwayat pengaktifan.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($totalPages > 1)
    <div style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f1f5f9;">
        <span style="font-size:13px;color:#64748b;">Halaman {{ $page }} / {{ $totalPages }}</span>
        <div style="display:flex;gap:6px;">
            <a href="{{ route('server-ujian.panel-pengawas', ['view'=>'history','page'=>max(1,$page-1)]) }}" class="btn btn-secondary btn-sm">Sebelumnya</a>
            <a href="{{ route('server-ujian.panel-pengawas', ['view'=>'history','page'=>min($totalPages,$page+1)]) }}" class="btn btn-secondary btn-sm">Selanjutnya</a>
        </div>
    </div>
    @endif
</div>

@elseif($view === 'top_blocked')
<div class="card" style="padding:0;overflow:hidden;">
    <div style="padding:16px 18px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f1f5f9;">
        <p style="font-weight:700;color:#334155;margin:0;">Ranking Peserta Terblokir Terbanyak</p>
        <span style="background:#fff7ed;color:#c2410c;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;">Top 20</span>
    </div>
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;width:60px;">Rank</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Peserta</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Total Frekuensi</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Terakhir Aktif</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topBlocked as $i => $row)
            @php $rank = $i + 1; @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;text-align:center;">
                    @if($rank==1) 🥇 @elseif($rank==2) 🥈 @elseif($rank==3) 🥉 @else <span style="font-weight:700;color:#94a3b8;">#{{ $rank }}</span> @endif
                </td>
                <td style="padding:10px 16px;font-size:13px;font-weight:700;">{{ $row->nama }}</td>
                <td style="padding:10px 16px;text-align:center;">
                    <span style="padding:4px 14px;border-radius:20px;font-size:13px;font-weight:800;background:{{ $row->total_pelanggaran >= 5 ? '#dc2626' : '#fed7aa' }};color:{{ $row->total_pelanggaran >= 5 ? '#fff' : '#c2410c' }};">{{ $row->total_pelanggaran }} Kali</span>
                </td>
                <td style="padding:10px 16px;font-size:13px;color:#94a3b8;font-family:monospace;">{{ (new DateTime($row->terakhir_aktif))->format('d/m H:i') }}</td>
                <td style="padding:10px 16px;">
                    @if($row->total_pelanggaran >= 5) <span style="font-size:11px;font-weight:700;color:#dc2626;">● KRITIS</span>
                    @elseif($row->total_pelanggaran >= 3) <span style="font-size:11px;font-weight:700;color:#f97316;">● PERINGATAN</span>
                    @else <span style="font-size:11px;font-weight:700;color:#94a3b8;">● NORMAL</span> @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Data ranking belum tersedia.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection
