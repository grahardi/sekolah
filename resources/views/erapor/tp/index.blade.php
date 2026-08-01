@extends('layouts.erapor')
@section('title', 'Tujuan Pembelajaran')
@section('page-title', 'Tujuan Pembelajaran (TP)')

@section('header-actions')
    <a href="{{ route('erapor.tp.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Tambah TP</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    TP adalah unit kompetensi terkecil per mata pelajaran (Kurikulum Merdeka). Nilai Sumatif TP
    dikaitkan ke TP tertentu di sini, dipakai untuk hitung deskripsi capaian kompetensi otomatis di rapor.
</p>

<form method="GET" class="card" style="padding:14px;margin-bottom:16px;display:flex;gap:10px;align-items:end;">
    <div style="flex:1;">
        <label class="form-label">Filter Mata Pelajaran</label>
        <select name="mapel_id" class="form-input" onchange="this.form.submit()">
            <option value="">Semua Mapel</option>
            @foreach($mapelList as $m)
            <option value="{{ $m->id }}" {{ (string)$filterMapel === (string)$m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Kode</th><th style="padding:10px;">Deskripsi TP</th><th style="padding:10px;">Mapel</th><th style="padding:10px;">Semester</th><th style="padding:10px;">Kelas</th><th></th>
        </tr></thead>
        <tbody>
            @forelse($tps as $tp)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;color:#2563EB;">{{ $tp->kode_tp ?? '-' }}</td>
                <td style="padding:10px;max-width:340px;">{{ Str::limit($tp->deskripsi_tp, 100) }}</td>
                <td style="padding:10px;">{{ $tp->mataPelajaran->nama }}</td>
                <td style="padding:10px;">{{ $tp->semester == 1 ? 'Ganjil' : 'Genap' }}</td>
                <td style="padding:10px;">
                    <div style="display:flex;flex-wrap:wrap;gap:3px;">
                        @foreach($tp->kelas_array as $k)<span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $k }}</span>@endforeach
                    </div>
                </td>
                <td style="padding:10px;text-align:right;">
                    <form action="{{ route('erapor.tp.destroy', $tp) }}" method="POST" onsubmit="return confirm('Hapus TP ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada Tujuan Pembelajaran.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:14px 18px;">{{ $tps->links() }}</div>
</div>
@endsection
