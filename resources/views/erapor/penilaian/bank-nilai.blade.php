@extends('layouts.erapor')
@section('title', 'Bank Nilai - ' . $mapel->nama)
@section('page-title', $mapel->nama)

@section('header-actions')
    <a href="{{ route('erapor.penilaian.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Ganti Kelas</a>
    <a href="{{ route('erapor.penilaian.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Buat Penilaian</a>
@endsection

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:20px 24px;margin-bottom:20px;color:#fff;">
    <p style="font-size:12px;opacity:.8;margin:0;">Bank Nilai</p>
    <p style="font-size:20px;font-weight:800;margin:0;">Kelas {{ $rombel ? "$kelas - $rombel" : $kelas }}</p>
</div>

<div class="card" style="padding:16px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Template Kelas (Semua Penilaian Sekaligus)</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 10px;">Download 1 file berisi SEMUA penilaian (termasuk PTS/UAS) sebagai kolom terpisah, isi semua nilai sekaligus, upload lagi.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('erapor.penilaian.template-kelas', ['mata_pelajaran_id' => $mapel->id, 'kelas_rombel' => $kelasRombel]) }}" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template Kelas</a>
        <form action="{{ route('erapor.penilaian.import-kelas') }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="hidden" name="mata_pelajaran_id" value="{{ $mapel->id }}">
            <input type="hidden" name="kelas_rombel" value="{{ $kelasRombel }}">
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size:12px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-upload"></i> Import Nilai Kelas</button>
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
    <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <thead>
            <tr style="text-align:center;color:#64748b;font-size:10px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
                <th rowspan="2" style="padding:8px;">No</th>
                <th rowspan="2" style="padding:8px;text-align:left;">Nama Siswa</th>
                <th colspan="{{ $sumatif->count() }}" style="padding:8px;border-bottom:1px solid #e2e8f0;">Nilai Sumatif</th>
                <th rowspan="2" style="padding:8px;">Nilai Rapor</th>
                <th rowspan="2" style="padding:8px;text-align:left;">Deskripsi Capaian Kompetensi (Rapor)</th>
            </tr>
            <tr style="text-align:center;color:#64748b;font-size:10px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                @foreach($sumatif as $p)<th style="padding:6px;">{{ $p->nama_penilaian }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $i => $r)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:8px;text-align:center;color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="padding:8px;font-weight:700;white-space:nowrap;">{{ $r['siswa']->nama_lengkap }}</td>
                @foreach($sumatif as $p)
                <td style="padding:8px;text-align:center;">{{ $r['nilai_per_penilaian'][$p->id] ?? '-' }}</td>
                @endforeach
                <td style="padding:8px;text-align:center;">
                    @if($r['nilai_rapor'] !== null)
                    <span class="badge badge-aktif" style="font-size:12px;padding:4px 10px;">{{ $r['nilai_rapor'] }}</span>
                    @else
                    <span style="color:#cbd5e1;">-</span>
                    @endif
                </td>
                <td style="padding:8px;color:#374151;max-width:340px;">{{ $r['deskripsi'] ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ 4 + $sumatif->count() }}" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
