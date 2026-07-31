@extends('layouts.kepegawaian')
@section('title', 'Data Pegawai')
@section('page-title', 'Data Pegawai')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('pegawai.import.form') }}" class="btn btn-secondary"><i class="ti ti-file-import"></i> Import Excel</a>
    <a href="{{ route('pegawai.export.choice') }}" class="btn btn-secondary"><i class="ti ti-download"></i> Export</a>
    <a href="{{ route('pegawai.create') }}" class="btn btn-primary"><i class="ti ti-user-plus"></i> Tambah Pegawai</a>
    @endif
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">Kelola data seluruh pegawai, guru, dan staff sekolah.</p>

<div class="card" style="padding:18px; margin-bottom:18px;">
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;align-items:end;">
        <div>
            <label class="form-label">Status Aktif</label>
            <select name="status_aktif" class="form-input" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['Aktif','Cuti','Nonaktif','Pensiun','Pindah'] as $s)
                <option value="{{ $s }}" {{ ($filters['status_aktif'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status Kepegawaian</label>
            <select name="jenis_kepegawaian" class="form-input" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['PNS','PPPK','GTT','PTT','GTY','PTY','Lainnya'] as $j)
                <option value="{{ $j }}" {{ ($filters['jenis_kepegawaian'] ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Unit Kerja</label>
            <select name="unit_kerja" class="form-input" onchange="this.form.submit()">
                <option value="">Semua Unit</option>
                @foreach($unitKerjaList as $u)
                <option value="{{ $u }}" {{ ($filters['unit_kerja'] ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label class="form-label">Pencarian</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari Nama, NIP, atau Jabatan..." class="form-input">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-search"></i> Filter</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <p style="font-size:13px;color:#64748b;margin:0;">
            Menampilkan {{ $pegawais->firstItem() ?? 0 }}–{{ $pegawais->lastItem() ?? 0 }} dari {{ $pegawais->total() }} pegawai
            @if($pegawais->total() != $totalSemua) (filtered from {{ $totalSemua }} total entries) @endif
        </p>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">Pegawai</th>
                    <th style="padding:10px;">Jabatan &amp; Golongan</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Kontak</th>
                    @if(auth()->user()->isAdmin())<th style="padding:10px 18px; text-align:right;">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $p)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:12px 18px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                                <img src="{{ $p->foto_url }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div>
                                <a href="{{ route('pegawai.show', $p) }}" style="font-weight:700;color:#0f172a;text-decoration:none;">{{ $p->nama_lengkap }}</a>
                                <p style="font-size:11px;color:#94a3b8;margin:0;">NIP: {{ $p->nip_nuptk ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 10px;">
                        <p style="font-weight:600;color:#1e40af;margin:0;">{{ $p->jabatan ?? '-' }}</p>
                        @if($p->isAsn())
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->pangkat }} @if($p->golongan) · Gol. {{ $p->golongan }} @endif</p>
                        @else
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->jenis_kepegawaian }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 10px;">
                        <span class="badge {{ $p->status_aktif === 'Aktif' ? 'badge-aktif' : ($p->status_aktif === 'Pindah' ? 'badge-keluar' : 'badge-lulus') }}">{{ $p->status_aktif }}</span>
                    </td>
                    <td style="padding:12px 10px; color:#475569;">
                        @if($p->no_hp)<i class="ti ti-brand-whatsapp" style="color:#16a34a;"></i> {{ $p->no_hp }}@else -@endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:12px 18px; text-align:right;">
                        <a href="{{ route('pegawai.edit', $p) }}" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></a>
                        <form action="{{ route('pegawai.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data pegawai ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada data pegawai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px;">
        {{ $pegawais->links() }}
    </div>
</div>
@endsection
