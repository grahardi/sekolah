@extends('layouts.erapor')
@section('title', 'Tujuan Pembelajaran')
@section('page-title', 'Tujuan Pembelajaran (TP)')

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:24px 28px;margin-bottom:20px;color:#fff;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:14px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;margin:0 0 4px;">Bank Tujuan Pembelajaran (TP)</h2>
            <p style="font-size:13px;opacity:.85;margin:0;">Kelola semua TP yang kamu buat untuk tahun ajaran aktif.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="submit" form="form-hapus-massal" onclick="return confirm('Hapus semua TP yang dicentang?')" class="btn btn-danger btn-sm" style="background:rgba(220,38,38,.9);">
                <i class="ti ti-trash"></i> Hapus Pilihan
            </button>
            <a href="{{ route('erapor.tp.create') }}" class="btn btn-sm" style="background:#FBBF24;color:#1E293B;font-weight:700;">
                <i class="ti ti-square-plus"></i> Tambah TP Baru
            </a>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'guru' && $mapelList->isEmpty())
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:16px;">
    <p style="font-size:13px;color:#92400e;margin:0;">
        Belum ada penugasan mengajar yang ditetapkan admin untuk kelas yang kamu ajar.
        Hubungi admin sekolah untuk menetapkan penugasan mengajarmu dulu.
    </p>
</div>
@endif

<form method="GET" style="margin-bottom:16px;max-width:300px;">
    <select name="mapel_id" class="form-input" onchange="this.form.submit()">
        <option value="">Semua Mapel</option>
        @foreach($mapelList as $m)
        <option value="{{ $m->id }}" {{ (string)$filterMapel === (string)$m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
        @endforeach
    </select>
</form>

<form id="form-hapus-massal" action="{{ route('erapor.tp.destroy-massal') }}" method="POST">
    @csrf @method('DELETE')

    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 14px;width:30px;"><input type="checkbox" onclick="document.querySelectorAll('.tp-cb').forEach(c=>c.checked=this.checked)"></th>
                <th style="padding:10px;width:70px;">Semester</th>
                <th style="padding:10px;width:90px;">Kode TP</th>
                <th style="padding:10px;">Deskripsi</th>
                <th style="padding:10px;width:220px;">Ditugaskan di Kelas</th>
                <th style="padding:10px 14px;width:100px;text-align:right;">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($tps->groupBy('mataPelajaran.nama') as $namaMapel => $groupTp)
                <tr style="background:#eff6ff;">
                    <td colspan="6" style="padding:8px 14px;font-weight:700;color:#1e40af;font-size:12px;"><i class="ti ti-book-2"></i> {{ $namaMapel }}</td>
                </tr>
                @foreach($groupTp as $tp)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 14px;"><input type="checkbox" name="ids[]" value="{{ $tp->id }}" class="tp-cb"></td>
                    <td style="padding:10px;color:#64748b;font-weight:600;">{{ $tp->semester }}</td>
                    <td style="padding:10px;">
                        @if($tp->kode_tp)<span class="badge" style="background:#1E3A5F;color:#fff;">{{ $tp->kode_tp }}</span>@endif
                    </td>
                    <td style="padding:10px;max-width:380px;color:#374151;">{{ $tp->deskripsi_tp }}</td>
                    <td style="padding:10px;">
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($tp->kelas_array as $k)<span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $k }}</span>@endforeach
                        </div>
                    </td>
                    <td style="padding:10px 14px;text-align:right;white-space:nowrap;">
                        <a href="{{ route('erapor.tp.edit', $tp) }}" class="btn btn-secondary btn-sm" title="Edit"><i class="ti ti-pencil"></i></a>
                        <a href="{{ route('erapor.tp.penugasan-kelas', $tp) }}" class="btn btn-sm" style="background:#eff6ff;color:#2563EB;" title="Penugasan Kelas"><i class="ti ti-school"></i></a>
                        <button type="button" onclick="if(confirm('Hapus TP ini?')){document.getElementById('hapus-{{ $tp->id }}').submit();}" class="btn btn-danger btn-sm" title="Hapus"><i class="ti ti-trash"></i></button>
                    </td>
                </tr>
                @endforeach
                @empty
                <tr><td colspan="6" style="padding:24px;text-align:center;color:#94a3b8;">Belum ada Tujuan Pembelajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

@foreach($tps as $tp)
<form id="hapus-{{ $tp->id }}" action="{{ route('erapor.tp.destroy', $tp) }}" method="POST" style="display:none;">@csrf @method('DELETE')</form>
@endforeach
@endsection
