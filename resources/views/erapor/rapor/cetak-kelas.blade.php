@extends('layouts.erapor')
@section('title', 'Cetak Rapor')
@section('page-title', 'Cetak Rapor - Kelas ' . $kelas . ($rombel ? " - $rombel" : ''))

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
    <div class="card" style="padding:18px;">
        <span class="badge" style="background:#eff6ff;color:#2563EB;margin-bottom:8px;display:inline-block;">Langkah 1</span>
        <p style="font-weight:700;color:#0f172a;margin:0 0 4px;">Manajemen Status</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Kunci (finalisasi) rapor sebelum cetak, atau buka lagi kalau mau diedit.</p>
        <div style="display:flex;gap:8px;">
            <form action="{{ route('erapor.rapor.finalisasi-semua') }}" method="POST" onsubmit="return confirm('Finalisasi semua rapor di kelas ini? Nilai tidak akan berubah lagi sampai dibatalkan.')">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelas }}"><input type="hidden" name="rombel" value="{{ $rombel }}"><input type="hidden" name="semester" value="{{ $semester }}">
                <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-lock"></i> Finalisasi Semua</button>
            </form>
            <form action="{{ route('erapor.rapor.batalkan-finalisasi-semua') }}" method="POST" onsubmit="return confirm('Batalkan finalisasi semua rapor di kelas ini?')">
                @csrf
                <input type="hidden" name="kelas" value="{{ $kelas }}"><input type="hidden" name="rombel" value="{{ $rombel }}"><input type="hidden" name="semester" value="{{ $semester }}">
                <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-lock-open"></i> Batalkan Finalisasi</button>
            </form>
        </div>
    </div>
    <div class="card" style="padding:18px;">
        <span class="badge" style="background:#f0fdf4;color:#16a34a;margin-bottom:8px;display:inline-block;">Langkah 2</span>
        <p style="font-weight:700;color:#0f172a;margin:0 0 4px;">Hitung Ulang Nilai</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Cuma berlaku utk siswa yang rapornya masih Draft (belum final).</p>
        <form action="{{ route('erapor.rapor.generate') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas_rombel" value="{{ $kelas }}|{{ $rombel }}"><input type="hidden" name="semester" value="{{ $semester }}">
            <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-calculator"></i> Hitung/Perbarui Nilai</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">Daftar Siswa</p></div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Siswa</th><th style="padding:10px;">NISN</th><th style="padding:10px;">Status Rapor</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($siswaList as $siswa)
            @php $rapor = $raporMap->get($siswa->id); @endphp
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                <td style="padding:10px;color:#94a3b8;font-family:monospace;">{{ $siswa->nisn }}</td>
                <td style="padding:10px;">
                    @if(!$rapor)<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Belum dihitung</span>
                    @elseif($rapor->status === 'Final')<span class="badge badge-aktif"><i class="ti ti-lock"></i> Final</span>
                    @else<span class="badge" style="background:#fef3c7;color:#92400e;">Draft</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    @if($rapor)
                    <a href="{{ route('erapor.rapor.edit', $rapor) }}" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i> Kelola</a>
                    <a href="{{ route('erapor.rapor.cetak', $rapor) }}" class="btn btn-primary btn-sm"><i class="ti ti-printer"></i> Cetak PDF</a>
                    @endif
                    <a href="{{ route('erapor.siswa.cetak-uts', $siswa) }}" class="btn btn-sm" style="background:#0891b2;color:#fff;"><i class="ti ti-qrcode"></i> Cetak UTS</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
