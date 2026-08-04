@extends('layouts.manajemen-sekolah')
@section('title', 'Isi Keterlambatan')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Isi Keterlambatan</h2>
    <a href="{{ route('manajemen-sekolah.keterlambatan.list') }}" class="btn btn-secondary"><i class="ti ti-list"></i> Lihat Rekap Terlambat</a>
</div>

<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:2fr 1fr;gap:12px;">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama siswa..." class="form-input">
    <select name="kelas" class="form-input" onchange="this.form.submit()">
        <option value="">-- Semua kelas --</option>
        @foreach($daftarKelas as $k)
        <option value="{{ $k }}" {{ $kelasFilter === $k ? 'selected' : '' }}>{{ $k }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary" style="grid-column:1/-1;justify-content:center;"><i class="ti ti-search"></i> Cari</button>
</form>

@if($siswaList->count() > 0)
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Status Hari Ini</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @foreach($siswaList as $s)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $s->nama_lengkap }}</td>
                <td style="padding:10px;">{{ $s->rombel ? "{$s->kelas}-{$s->rombel}" : $s->kelas }}</td>
                <td style="padding:10px;">
                    @if($s->absenHariIni)<span class="badge" style="background:#fef3c7;color:#92400e;">Sudah Absen ({{ $s->absenHariIni->status }})</span>
                    @elseif($s->telatHariIni)<span class="badge" style="background:#fecaca;color:#b91c1c;">Sudah Terlambat</span>
                    @else<span class="badge" style="background:#f1f5f9;color:#64748b;">Belum ada catatan</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    @if(!$s->absenHariIni && !$s->telatHariIni)
                    <form action="{{ route('manajemen-sekolah.keterlambatan.tandai', $s) }}" method="POST" style="display:inline-flex;gap:6px;">
                        @csrf
                        <input type="text" name="keterangan" placeholder="Keterangan (opsional)" class="form-input" style="font-size:12px;padding:6px 8px;width:160px;">
                        <button type="submit" class="btn btn-sm" style="background:#fecaca;color:#b91c1c;"><i class="ti ti-clock"></i> Tandai Terlambat</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@elseif($cari || $kelasFilter)
<p style="text-align:center;color:#94a3b8;padding:30px;">Tidak ada siswa ditemukan.</p>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Cari nama siswa atau pilih kelas dulu di atas.</p>
@endif
@endsection
