@extends('layouts.server-ujian')
@section('title', 'Monitoring Ujian Ruangan')
@section('page-title', 'Monitoring Ruang ' . $filterRuang)

@php
if (! function_exists('statusStyleMonitoring')) {
    function statusStyleMonitoring($status, $laporan = 0) {
        if ($laporan == 1) return ['bg' => '#fef2f2', 'border' => '#dc2626', 'text' => 'ERR', 'dot' => '#dc2626'];
        if ($status === 1) return ['bg' => '#eff6ff', 'border' => '#60a5fa', 'text' => 'ON', 'dot' => '#3b82f6'];
        if ($status !== null && (int)$status >= 2) return ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => 'DONE', 'dot' => '#16a34a'];
        return ['bg' => '#f8fafc', 'border' => '#e2e8f0', 'text' => 'OFF', 'dot' => '#cbd5e1'];
    }
}
@endphp

@section('content')

<meta http-equiv="refresh" content="15">

<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div style="display:flex;flex-wrap:wrap;gap:16px;justify-content:space-between;align-items:center;">
        <div>
            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">
                Tipe Alur: {{ $tipeRuangan }} | Jadwal: {{ $filterJadwal ? substr((string)$filterJadwal, 0, 8) . '...' : 'TIDAK ADA JADWAL AKTIF' }}
            </p>
        </div>
        <div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;margin-bottom:8px;">
                @forelse($allJadwalAktif as $j)
                <a href="{{ route('server-ujian.monitoring-ruangan', ['ruang'=>$filterRuang,'jadwal_id'=>$j->id]) }}"
                   style="padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;{{ $filterJadwal == $j->id ? 'background:#1e293b;color:#fff;' : 'background:#fff;color:#64748b;border:1px solid #e2e8f0;' }}">{{ $j->alias }}</a>
                @empty
                <span style="padding:6px 12px;border-radius:8px;font-size:11px;font-weight:700;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;">BELUM ADA JADWAL AKTIF</span>
                @endforelse
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;">
                @for($i = 1; $i <= 20; $i++)
                <a href="{{ route('server-ujian.monitoring-ruangan', ['ruang'=>$i,'jadwal_id'=>$filterJadwal]) }}"
                   style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:10px;font-weight:700;text-decoration:none;{{ $filterRuang == $i ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#94a3b8;border:1px solid #e2e8f0;' }}">{{ $i }}</a>
                @endfor
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:24px;">
    @foreach(['total'=>'Total','mengerjakan'=>'Ujian','selesai'=>'Selesai','belum_login'=>'Pasif','error'=>'Kendala'] as $key => $lb)
    <div class="card" style="padding:12px;text-align:center;">
        <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0;">{{ $lb }}</p>
        <p style="font-size:22px;font-weight:900;color:#1e293b;margin:2px 0 0;">{{ $stats[$key] }}</p>
    </div>
    @endforeach
</div>

<div style="width:100%;background:#1e293b;padding:8px;text-align:center;border-radius:10px 10px 0 0;margin-bottom:24px;">
    <span style="font-size:10px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.3em;">PAPAN TULIS / LAYAR</span>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
    @foreach($matriks as $barisMeja)
    @foreach($barisMeja as $noMeja)
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:4px;text-align:center;">
            <span style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;">Meja {{ $noMeja }}</span>
        </div>
        <div style="display:flex;min-height:200px;">
            @foreach(['A','B'] as $pos)
            @php
                $info = $mejaData[$noMeja][$pos] ?? null;
                $st = statusStyleMonitoring($info->status_kerja ?? null, $info->laporan ?? 0);
                $fotoUrl = null;
                if ($info && $info->foto) {
                    if (str_starts_with($info->foto, 'http')) {
                        $fotoUrl = $info->foto;
                    } else {
                        $folder = str_contains($info->foto, '/') ? '' : '8new/';
                        $fotoUrl = 'https://siswa.sekolah.co.id/images/' . $folder . $info->foto;
                    }
                }
            @endphp
            <div style="flex:1;padding:10px;display:flex;flex-direction:column;align-items:center;position:relative;border-left:{{ $pos === 'B' ? '1px solid #f1f5f9' : 'none' }};{{ $info ? 'cursor:pointer;' : '' }}"
                 @if($info) onclick="openLaporModal('{{ addslashes($info->nama) }}', '{{ $info->no_ujian }}')" @endif>
                @if($info)
                <div style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:{{ $st['dot'] }};"></div>
                <div style="width:100%;aspect-ratio:3/4;background:#e2e8f0;border-radius:8px;margin-bottom:8px;overflow:hidden;{{ $fotoUrl ? "background-image:url('{$fotoUrl}');background-size:contain;background-position:center;background-repeat:no-repeat;" : '' }}"></div>
                <p style="font-size:10px;font-weight:800;text-transform:uppercase;margin:0 0 2px;text-align:center;">{{ explode(' ', trim($info->nama))[0] }}</p>
                <p style="font-size:8px;font-weight:700;color:#94a3b8;margin:0 0 6px;">{{ $info->no_ujian }}</p>
                <div style="margin-top:auto;">
                    @if(($info->status_kerja ?? null) == 1 && $info->laporan == 0)
                    <div style="background:#2563eb;color:#fff;font-size:9px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ floor($info->sisa_waktu/60) }} MIN</div>
                    @else
                    <div style="background:{{ $st['bg'] }};border:1px solid {{ $st['border'] }};font-size:8px;font-weight:800;padding:2px 8px;border-radius:4px;text-transform:uppercase;">{{ $st['text'] }}</div>
                    @endif
                </div>
                @else
                <div style="height:100%;display:flex;align-items:center;justify-content:center;opacity:.15;padding:40px 0;">
                    <span style="font-size:9px;font-weight:800;transform:rotate(-45deg);">KOSONG</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endforeach
</div>

<div id="laporModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:320px;width:100%;padding:0;overflow:hidden;">
        <div style="background:#dc2626;padding:18px;color:#fff;text-align:center;">
            <p style="font-size:16px;font-weight:800;text-transform:uppercase;margin:0;">Tandai Error?</p>
        </div>
        <div style="padding:20px;text-align:center;">
            <p id="modalSiswaNama" style="font-weight:800;font-size:13px;text-transform:uppercase;margin:0 0 20px;"></p>
            <form method="POST" action="{{ route('server-ujian.monitoring-ruangan.lapor-error') }}">
                @csrf
                <input type="hidden" name="no_ujian_lapor" id="inputNoUjian">
                <button type="submit" style="width:100%;background:#dc2626;color:#fff;font-weight:800;padding:12px;border-radius:10px;border:none;font-size:11px;text-transform:uppercase;margin-bottom:8px;cursor:pointer;">Ya, Tandai Kendala</button>
                <button type="button" onclick="document.getElementById('laporModal').style.display='none'" style="width:100%;background:#f1f5f9;color:#94a3b8;font-weight:700;padding:12px;border-radius:10px;border:none;font-size:11px;text-transform:uppercase;cursor:pointer;">Batal</button>
            </form>
        </div>
    </div>
</div>

<script>
function openLaporModal(nama, no) {
    document.getElementById('modalSiswaNama').innerText = nama;
    document.getElementById('inputNoUjian').value = no;
    document.getElementById('laporModal').style.display = 'flex';
}
</script>

@endsection
