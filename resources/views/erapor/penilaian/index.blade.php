@extends('layouts.erapor')
@section('title', 'Input Nilai')
@section('page-title', 'Penilaian & Input Nilai')

@section('header-actions')
    <a href="{{ route('erapor.penilaian.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Buat Penilaian</a>
@endsection

@section('content')
@if(auth()->user()->role === 'guru' && $mapelList->isEmpty())
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:16px;">
    <p style="font-size:13px;color:#92400e;margin:0;">
        Belum ada Tujuan Pembelajaran/penugasan mengajar yang ditetapkan admin untuk kelas yang kamu ajar.
        Hubungi admin sekolah untuk menetapkan penugasan mengajarmu dulu.
    </p>
</div>
@endif

<form method="GET" class="card" style="padding:14px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
    <div>
        <label class="form-label">Mata Pelajaran</label>
        <select name="mata_pelajaran_id" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Mapel</option>
            @foreach($mapelList as $m)<option value="{{ $m->id }}" {{ (string)($filters['mata_pelajaran_id'] ?? '') === (string)$m->id ? 'selected' : '' }}>{{ $m->nama }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Kelas</label>
        <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            @foreach($kelasList as $k)@php [$kl,$rb]=explode('|',$k);@endphp<option value="{{ $k }}" {{ ($filters['kelas_rombel'] ?? '') === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>@endforeach
        </select>
    </div>
</form>

@if(($filters['mata_pelajaran_id'] ?? null) && ($filters['kelas_rombel'] ?? null))
<div class="card" style="padding:16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Template Kelas (Semua Penilaian Sekaligus)</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 10px;">
        Download 1 file berisi SEMUA penilaian untuk mapel &amp; kelas yang dipilih (termasuk PTS/UAS) - tiap
        penilaian jadi kolom sendiri. Isi semua nilai sekaligus, lalu upload lagi.
    </p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('erapor.penilaian.template-kelas', ['mata_pelajaran_id' => $filters['mata_pelajaran_id'], 'kelas_rombel' => $filters['kelas_rombel']]) }}" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template Kelas</a>
        <form action="{{ route('erapor.penilaian.import-kelas') }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="hidden" name="mata_pelajaran_id" value="{{ $filters['mata_pelajaran_id'] }}">
            <input type="hidden" name="kelas_rombel" value="{{ $filters['kelas_rombel'] }}">
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size:12px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-upload"></i> Import</button>
        </form>
    </div>
</div>
@endif

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Penilaian</th><th style="padding:10px;">Mapel</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Jenis</th><th style="padding:10px;">Nilai Masuk</th><th></th>
        </tr></thead>
        <tbody>
            @forelse($penilaians as $p)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;">
                    <a href="{{ route('erapor.penilaian.show', $p) }}" style="font-weight:700;color:#0f172a;text-decoration:none;">{{ $p->nama_penilaian }}</a>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->guru->nama }}</p>
                </td>
                <td style="padding:10px;">{{ $p->mataPelajaran->nama }}</td>
                <td style="padding:10px;">{{ $p->kelas_lengkap }}</td>
                <td style="padding:10px;">
                    <span class="badge" style="background:{{ $p->jenis_penilaian === 'Sumatif' ? '#fef3c7' : '#eff6ff' }};color:{{ $p->jenis_penilaian === 'Sumatif' ? '#92400e' : '#2563EB' }};">
                        {{ $p->subjenis_label }}
                    </span>
                </td>
                <td style="padding:10px;">{{ $p->nilais_count }} siswa</td>
                <td style="padding:10px;text-align:right;">
                    <a href="{{ route('erapor.penilaian.show', $p) }}" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i> Input Nilai</a>
                    <form action="{{ route('erapor.penilaian.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus penilaian ini beserta semua nilainya?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada penilaian.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:14px 18px;">{{ $penilaians->links() }}</div>
</div>
@endsection
