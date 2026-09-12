@extends('layouts.server-ujian')
@section('title', 'Monitoring Ujian')
@section('page-title', 'Monitoring Ujian')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<meta http-equiv="refresh" content="15">

<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <div style="min-width:200px;">
            <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Jadwal Aktif</label>
            <select name="jadwal_id" class="form-input" onchange="this.form.submit()">
                @forelse($allJadwalAktif as $j)
                <option value="{{ $j->id }}" {{ $filterJadwal == $j->id ? 'selected' : '' }}>{{ $j->alias }}</option>
                @empty
                <option value="">Tidak ada jadwal aktif</option>
                @endforelse
            </select>
        </div>
        <div style="min-width:220px;">
            <label style="font-size:11px;color:#64748b;display:block;margin-bottom:4px;">Kelas / Kelompok</label>
            <select name="grup_id" class="form-input" onchange="this.form.submit()">
                @forelse($subGrupList as $g)
                <option value="{{ $g->id }}" {{ $filterGrup == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @empty
                <option value="">Belum ada kelompok tersinkron</option>
                @endforelse
            </select>
        </div>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:24px;">
    @foreach(['total'=>'Total','mengerjakan'=>'Sedang Ujian','selesai'=>'Selesai','belum_login'=>'Belum Login'] as $key => $lb)
    <div class="card" style="padding:12px;text-align:center;">
        <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">{{ $lb }}</p>
        <p style="font-size:22px;font-weight:900;color:#1e293b;margin:2px 0 0;">{{ $stats[$key] }}</p>
    </div>
    @endforeach
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;width:50px;"></th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">No. Ujian</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Sisa Waktu</th>
                <th style="padding:10px 16px;text-align:center;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesertaGrup as $p)
            @php
                $status = $p->status_ujian;
                if ($status === null) { $badgeBg = '#f8fafc'; $badgeText = '#94a3b8'; $label = 'BELUM LOGIN'; }
                elseif ((int)$status >= 2) { $badgeBg = '#f0fdf4'; $badgeText = '#16a34a'; $label = 'SELESAI'; }
                else { $badgeBg = '#eff6ff'; $badgeText = '#3b82f6'; $label = 'MENGERJAKAN'; }
                $fotoUrl = $p->ava ? $fotoBaseUrl . $p->ava : null;
            @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:8px 16px;">
                    <div style="width:34px;height:34px;border-radius:8px;overflow:hidden;background:#e2e8f0;{{ $fotoUrl ? "background-image:url('{$fotoUrl}');background-size:cover;background-position:center;" : '' }}"></div>
                </td>
                <td style="padding:8px 16px;font-size:13px;font-weight:600;">{{ $p->nama }}</td>
                <td style="padding:8px 16px;font-size:12px;color:#94a3b8;font-family:monospace;">{{ $p->no_ujian }}</td>
                <td style="padding:8px 16px;text-align:center;"><span style="background:{{ $badgeBg }};color:{{ $badgeText }};font-size:10px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ $label }}</span></td>
                <td style="padding:8px 16px;text-align:center;font-size:12px;color:#64748b;">{{ $p->sisa_waktu ? floor($p->sisa_waktu / 60) . ' menit' : '-' }}</td>
                <td style="padding:8px 16px;text-align:center;">
                    <form method="POST" action="{{ route('server-ujian.monitoring-ruangan.lapor-error') }}" onsubmit="return confirm('Tandai {{ addslashes($p->nama) }} mengalami kendala?')" style="display:inline;">
                        @csrf
                        <input type="hidden" name="no_ujian_lapor" value="{{ $p->no_ujian }}">
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;">Lapor Kendala</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;font-style:italic;">Belum ada peserta di kelompok ini, atau belum ada jadwal/kelompok tersinkron.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
