@extends('layouts.manajemen-sekolah')
@section('title', 'Rekap Absensi Bulanan')
@section('page-title', 'Rekap Absensi Bulanan')

@section('content')
<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label class="form-label">Bulan</label>
        <input type="month" name="bulan" value="{{ $bulan }}" class="form-input" onchange="this.form.submit()">
    </div>
    <div>
        <label class="form-label">Kelas</label>
        <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
            <option value="">-- Pilih kelas --</option>
            @foreach($kelasList as $k)
            @php [$kl,$rb]=explode('|',$k); @endphp
            <option value="{{ $k }}" {{ $kelasRombel === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
            @endforeach
        </select>
    </div>
</form>

@if($kelasRombel)
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:center;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;text-align:left;">Nama Siswa</th>
            <th style="padding:10px;">Hadir</th><th style="padding:10px;">Sakit</th><th style="padding:10px;">Izin</th><th style="padding:10px;">Alpha</th><th style="padding:10px;">Dispensasi</th>
        </tr></thead>
        <tbody>
            @forelse($rekap as $r)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $r['siswa']->nama_lengkap }}</td>
                <td style="padding:10px;text-align:center;color:#16a34a;font-weight:700;">{{ $r['hadir'] }}</td>
                <td style="padding:10px;text-align:center;color:#dc2626;">{{ $r['sakit'] }}</td>
                <td style="padding:10px;text-align:center;color:#d97706;">{{ $r['izin'] }}</td>
                <td style="padding:10px;text-align:center;color:#64748b;font-weight:700;">{{ $r['alpha'] }}</td>
                <td style="padding:10px;text-align:center;color:#2563EB;">{{ $r['dispensasi'] }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Pilih kelas dulu di atas.</p>
@endif
@endsection
