@extends('layouts.erapor')
@section('title', 'Cetak Rapor')
@section('page-title', 'Rapor Siswa')

@section('content')
@if(!$tahunAjaran)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;">
    <p style="font-size:13px;color:#92400e;margin:0;">Belum ada tahun ajaran aktif. <a href="{{ route('erapor.tahun-ajaran') }}" style="text-decoration:underline;">Atur di sini</a> dulu.</p>
</div>
@else
<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label class="form-label">Kelas</label>
        <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
            <option value="">-- Pilih kelas --</option>
            @foreach($kelasList as $k)
            @php [$kl,$rb]=explode('|',$k);@endphp
            <option value="{{ $k }}" {{ $kelasRombel === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Semester</label>
        <select name="semester" class="form-input" onchange="this.form.submit()">
            <option value="1" {{ $semester == 1 ? 'selected' : '' }}>1 (Ganjil)</option>
            <option value="2" {{ $semester == 2 ? 'selected' : '' }}>2 (Genap)</option>
        </select>
    </div>
</form>

@if($kelasRombel)
<form action="{{ route('erapor.rapor.generate') }}" method="POST" style="margin-bottom:12px;display:inline-block;">
    @csrf
    <input type="hidden" name="kelas_rombel" value="{{ $kelasRombel }}">
    <input type="hidden" name="semester" value="{{ $semester }}">
    <button type="submit" class="btn btn-primary"><i class="ti ti-calculator"></i> Hitung/Perbarui Nilai Semua Siswa di Kelas Ini</button>
</form>

<div class="card" style="padding:16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Absensi Massal (Import Excel)</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 10px;">Download template berisi NISN/Nama/Kelas kelas ini, isi kolom S/I/A, lalu upload lagi.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('erapor.rapor.template-absensi', ['kelas_rombel' => $kelasRombel, 'semester' => $semester]) }}" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template Absensi</a>
        <form action="{{ route('erapor.rapor.import-absensi') }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size:12px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-upload"></i> Import Absensi</button>
        </form>
    </div>
</div>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Siswa</th><th style="padding:10px;">Status Rapor</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($siswaList as $siswa)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                <td style="padding:10px;">
                    @if(!$siswa->rapor)<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Belum dihitung</span>
                    @elseif($siswa->rapor->status === 'Final')<span class="badge badge-aktif">Final</span>
                    @else<span class="badge" style="background:#fef3c7;color:#92400e;">Draft</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    @if($siswa->rapor)
                    <a href="{{ route('erapor.rapor.edit', $siswa->rapor) }}" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i> Kelola</a>
                    <a href="{{ route('erapor.rapor.cetak', $siswa->rapor) }}" class="btn btn-primary btn-sm"><i class="ti ti-printer"></i> Cetak PDF</a>
                    <a href="{{ route('erapor.siswa.cetak-uts', $siswa) }}" class="btn btn-sm" style="background:#0891b2;color:#fff;"><i class="ti ti-qrcode"></i> Cetak UTS</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endif
@endsection
