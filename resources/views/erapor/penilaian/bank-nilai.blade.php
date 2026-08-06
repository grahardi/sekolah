@extends('layouts.erapor')
@section('title', 'Bank Nilai - ' . $mapel->nama)
@section('page-title', $mapel->nama)

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:24px 28px;margin-bottom:20px;color:#fff;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;margin-bottom:18px;">
        <div>
            <h2 style="font-size:24px;font-weight:800;margin:0 0 4px;">{{ $mapel->nama }}</h2>
            <p style="font-size:13px;opacity:.85;margin:0;">Bank Nilai - Kelas {{ $rombel ? "$kelas - $rombel" : $kelas }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('erapor.penilaian.index') }}" class="btn btn-sm" style="background:transparent;border:1px solid rgba(255,255,255,.5);color:#fff;">
                <i class="ti ti-arrows-exchange"></i> Ganti
            </a>
            <a href="{{ route('erapor.penilaian.create', ['mata_pelajaran_id' => $mapel->id, 'kelas_rombel' => $kelasRombel]) }}" class="btn btn-sm" style="background:#fff;color:#1E3A5F;font-weight:700;">
                <i class="ti ti-square-plus"></i> Buat Penilaian
            </a>
        </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,.2);padding-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('erapor.penilaian.template-kelas', ['mata_pelajaran_id' => $mapel->id, 'kelas_rombel' => $kelasRombel]) }}" class="btn" style="background:#fff;color:#1E3A5F;font-weight:600;padding:10px 18px;">
            <i class="ti ti-download"></i> Download Template Kelas
        </a>
        <button type="button" onclick="document.getElementById('modal-import').style.display='flex'" class="btn" style="background:#fff;color:#1E3A5F;font-weight:600;padding:10px 18px;">
            <i class="ti ti-upload"></i> Import Nilai Kelas
        </button>
    </div>
</div>

<div id="modal-import" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:460px;width:100%;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Import Nilai Kelas</p>
            <button type="button" onclick="document.getElementById('modal-import').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 16px;">Upload file template kelas yang sudah diisi nilainya (kolom no_induk, nama, dan tiap penilaian).</p>
        <form action="{{ route('erapor.penilaian.import-kelas') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="mata_pelajaran_id" value="{{ $mapel->id }}">
            <input type="hidden" name="kelas_rombel" value="{{ $kelasRombel }}">
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-input" style="margin-bottom:16px;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-upload"></i> Import Sekarang</button>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-chart-bar"></i> Daftar Penilaian Sumatif</p></div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;width:40px;">No</th><th style="padding:10px;width:180px;">Nama Penilaian</th><th style="padding:10px;">Tujuan Pembelajaran (TP)</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($sumatif as $i => $p)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="padding:10px;">
                    <p style="font-weight:700;color:#0f172a;margin:0;">{{ $p->nama_penilaian }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;font-style:italic;">({{ $p->subjenis_label }} - Bobot: {{ $p->bobot_penilaian }})</p>
                </td>
                <td style="padding:10px;color:#374151;">
                    @if($p->tujuanPembelajarans->count() > 0)
                    {{ $p->tujuanPembelajarans->pluck('deskripsi_tp')->implode('; ') }}
                    @else
                    <span style="color:#94a3b8;font-style:italic;">- Penilaian non-TP (mis. Sumatif Akhir Semester) -</span>
                    @endif
                </td>
                <td style="padding:10px 18px;text-align:right;white-space:nowrap;">
                    <a href="{{ route('erapor.penilaian.show', $p) }}" class="btn btn-sm" style="background:#16a34a;color:#fff;"><i class="ti ti-edit"></i> Input Nilai</a>
                    <a href="{{ route('erapor.penilaian.edit', $p) }}" class="btn btn-secondary btn-sm"><i class="ti ti-settings"></i> Edit</a>
                    <form action="{{ route('erapor.penilaian.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penilaian ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada penilaian Sumatif untuk kelas &amp; mapel ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-circle-check"></i> Daftar Penilaian Formatif</p></div>
    <div style="padding:20px;text-align:center;color:#d97706;font-size:13px;">
        Formatif dinonaktifkan sementara di sistem ini.
    </div>
</div>

<div class="card">
    <div class="card-header"><p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-report"></i> Ringkasan Nilai Akhir &amp; Rapor (Satu Kelas)</p></div>
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="text-align:center;color:#374151;font-size:11px;text-transform:uppercase;background:#f1f5f9;">
                <th rowspan="2" style="padding:10px;border:1px solid #e2e8f0;">No</th>
                <th rowspan="2" style="padding:10px;text-align:left;border:1px solid #e2e8f0;">Nama Siswa</th>
                <th colspan="{{ $sumatif->count() }}" style="padding:10px;border:1px solid #e2e8f0;background:#e2e8f0;">Nilai Sumatif</th>
                <th rowspan="2" style="padding:10px;border:1px solid #e2e8f0;background:#dcfce7;">Nilai Rapor</th>
                <th rowspan="2" style="padding:10px;text-align:left;border:1px solid #e2e8f0;background:#dcfce7;">Deskripsi Capaian Kompetensi (Rapor)</th>
            </tr>
            <tr style="text-align:center;color:#374151;font-size:11px;text-transform:uppercase;background:#f8fafc;">
                @foreach($sumatif as $p)<th style="padding:8px;border:1px solid #e2e8f0;">{{ $p->nama_penilaian }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $i => $r)
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:10px;text-align:center;color:#94a3b8;border:1px solid #f1f5f9;">{{ $i + 1 }}</td>
                <td style="padding:10px;font-weight:700;white-space:nowrap;border:1px solid #f1f5f9;">{{ $r['siswa']->nama_lengkap }}</td>
                @foreach($sumatif as $p)
                <td style="padding:10px;text-align:center;font-size:16px;font-weight:700;color:#0f172a;border:1px solid #f1f5f9;">{{ $r['nilai_per_penilaian'][$p->id] ?? '-' }}</td>
                @endforeach
                <td style="padding:10px;text-align:center;border:1px solid #f1f5f9;">
                    @if($r['nilai_rapor'] !== null)
                    @php
                        $nr = $r['nilai_rapor'];
                        if ($nr < $kkm) { $bg = '#fef9c3'; $fg = '#854d0e'; }
                        elseif ($nr > 95) { $bg = '#1e40af'; $fg = '#fff'; }
                        else { $bg = '#16a34a'; $fg = '#fff'; }
                    @endphp
                    <span style="display:inline-block;background:{{ $bg }};color:{{ $fg }};font-size:16px;font-weight:800;padding:6px 14px;border-radius:8px;">{{ $nr }}</span>
                    @else
                    <span style="color:#cbd5e1;">-</span>
                    @endif
                </td>
                <td style="padding:10px;color:#374151;max-width:340px;border:1px solid #f1f5f9;">{{ $r['deskripsi'] ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ 4 + $sumatif->count() }}" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
