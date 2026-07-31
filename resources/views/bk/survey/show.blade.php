@extends('layouts.bk')
@section('title', $survey->judul)
@section('page-title', $survey->judul)

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('bk.survey.edit', $survey) }}" class="btn btn-secondary"><i class="ti ti-pencil"></i> Edit</a>
    @endif
    <a href="{{ route('bk.survey.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="card" style="padding:20px;margin-bottom:16px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
        <span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $survey->jenis }}</span>
        @if($survey->status === 'aktif')<span class="badge badge-aktif">Aktif</span>
        @elseif($survey->status === 'draft')<span class="badge" style="background:#f1f5f9;color:#64748b;">Draft</span>
        @else<span class="badge badge-keluar">Ditutup</span>@endif
        <span style="font-size:12px;color:#94a3b8;">{{ $survey->target_kelas ?: 'Semua kelas aktif' }}</span>
    </div>
    @if($survey->deskripsi)<p style="font-size:13px;color:#64748b;margin-bottom:14px;">{{ $survey->deskripsi }}</p>@endif

    @if($survey->status === 'aktif')
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <i class="ti ti-link" style="font-size:20px;color:#2563EB;"></i>
        <input type="text" readonly value="{{ $survey->publicUrl() }}" id="public-link" style="flex:1;min-width:200px;border:none;background:transparent;font-size:13px;color:#1e40af;font-weight:600;">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('public-link').value); this.textContent='Tersalin!'" class="btn btn-primary btn-sm">Salin Link</button>
    </div>
    <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Bagikan link ini ke siswa (WhatsApp grup kelas, dsb). Siswa tidak perlu login.</p>
    @else
    <p style="font-size:12px;color:#94a3b8;">Ubah status jadi <strong>Aktif</strong> di halaman edit supaya link bisa diakses & diisi siswa.</p>
    @endif
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:16px;">
    <div class="card" style="padding:16px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Target Siswa</p>
        <p style="font-size:26px;font-weight:800;color:#0f172a;margin:2px 0 0;">{{ $siswaTarget->count() }}</p>
    </div>
    <div class="card" style="padding:16px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Sudah Mengisi</p>
        <p style="font-size:26px;font-weight:800;color:#16a34a;margin:2px 0 0;">{{ count($sudahIsi) }}</p>
    </div>
    <div class="card" style="padding:16px;">
        <p style="font-size:11px;color:#94a3b8;text-transform:uppercase;margin:0;">Belum Mengisi</p>
        <p style="font-size:26px;font-weight:800;color:#dc2626;margin:2px 0 0;">{{ $siswaTarget->count() - count($sudahIsi) }}</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">Progress Pengisian per Siswa</p></div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Siswa</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Status</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($siswaTarget as $siswa)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                <td style="padding:10px;">{{ $siswa->rombel_lengkap }}</td>
                <td style="padding:10px;">
                    @if(in_array($siswa->id, $sudahIsi))<span class="badge badge-aktif">Sudah Mengisi</span>
                    @else<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Belum</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    @if(in_array($siswa->id, $sudahIsi))
                    <a href="{{ route('bk.survey.hasil-siswa', [$survey, $siswa]) }}" class="btn btn-secondary btn-sm"><i class="ti ti-eye"></i> Lihat Jawaban</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa yang cocok dengan target kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
