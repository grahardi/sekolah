@extends('layouts.alumni')
@section('title', 'History Alumni')
@section('page-title', 'History & Statistik Alumni')

@section('content')

<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
    <p style="font-size:12px;color:#166534;margin:0;">Bagikan link ini ke alumni supaya mereka bisa isi data sendiri:</p>
    <input type="text" readonly value="{{ url("/{$npsn}/alumni") }}" onclick="this.select()" style="width:100%;margin-top:6px;padding:8px 10px;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;background:#fff;">
</div>

<form method="GET" style="margin-bottom:20px;max-width:240px;">
    <select name="tahun_lulus" class="form-input" onchange="this.form.submit()">
        <option value="">Semua Angkatan</option>
        @foreach($tahunList as $t)
        <option value="{{ $t }}" {{ (string) $tahunLulus === (string) $t ? 'selected' : '' }}>Angkatan {{ $t }}</option>
        @endforeach
    </select>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:14px;margin-bottom:20px;">
    <div class="card" style="padding:16px;text-align:center;">
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:0;">{{ $persenMengisi }}%</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Sudah Mengisi ({{ $sudahMengisi }}/{{ $total }})</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <p style="font-size:26px;font-weight:800;color:#16a34a;margin:0;">{{ $persenLanjut }}%</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Lanjut Sekolah ({{ $lanjutSekolah }})</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <p style="font-size:26px;font-weight:800;color:#7c3aed;margin:0;">{{ $pondokPesantren }}</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Pondok Pesantren</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <p style="font-size:26px;font-weight:800;color:#d97706;margin:0;">{{ $bekerja }}</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Bekerja</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;">
        <p style="font-size:26px;font-weight:800;color:#64748b;margin:0;">{{ $tidakMelanjutkan }}</p>
        <p style="font-size:11px;color:#64748b;margin:4px 0 0;">Tidak Melanjutkan</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
    <div class="card" style="padding:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Sekolah Favorit (Top 10)</p>
        @forelse($sekolahFavorit as $sf)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-top:1px solid #f1f5f9;">
            <span style="font-size:13px;color:#334155;">{{ $sf->nama ?: '-' }}</span>
            <span style="font-size:12px;font-weight:700;color:#16a34a;background:#f0fdf4;padding:2px 8px;border-radius:10px;">{{ $sf->jumlah }}</span>
        </div>
        @empty
        <p style="font-size:12px;color:#94a3b8;margin:0;">Belum ada data.</p>
        @endforelse
    </div>
    <div class="card" style="padding:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Jurusan Favorit (Top 10)</p>
        @forelse($jurusanFavorit as $jf)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-top:1px solid #f1f5f9;">
            <span style="font-size:13px;color:#334155;">{{ $jf->alumni_jurusan }}</span>
            <span style="font-size:12px;font-weight:700;color:#7c3aed;background:#f5f3ff;padding:2px 8px;border-radius:10px;">{{ $jf->jumlah }}</span>
        </div>
        @empty
        <p style="font-size:12px;color:#94a3b8;margin:0;">Belum ada data.</p>
        @endforelse
    </div>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Angkatan</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Diisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarAlumni as $a)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $a->nama_lengkap }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $a->tahun_lulus ?: '-' }}</td>
                <td style="padding:10px 16px;">
                    @if($a->alumni_diisi_at)
                    <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $a->alumni_label }}</span>
                    @else
                    <span style="background:#f1f5f9;color:#94a3b8;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Belum Mengisi</span>
                    @endif
                </td>
                <td style="padding:10px 16px;font-size:12px;color:#94a3b8;">{{ $a->alumni_diisi_at?->locale('id')->diffForHumans() ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada data alumni.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $daftarAlumni->links() }}</div>

@endsection
