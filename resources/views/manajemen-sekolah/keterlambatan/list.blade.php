@extends('layouts.manajemen-sekolah')
@section('title', 'Rekap Terlambat')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Siswa Terlambat</h2>
    <a href="{{ route('manajemen-sekolah.keterlambatan.index') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Isi Keterlambatan</a>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<form method="GET" style="margin-bottom:16px;max-width:260px;">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
</form>

<form action="{{ route('manajemen-sekolah.keterlambatan.hapus-massal') }}" method="POST" id="form-hapus-massal" onsubmit="return confirm('Hapus semua data yang dicentang? Tidak bisa dibatalkan.')">
    @csrf
    @method('DELETE')
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:12px 18px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <label style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" id="cb-semua" onclick="document.querySelectorAll('.cb-item').forEach(cb => cb.checked = this.checked)"> Centang Semua
            </label>
            <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;"><i class="ti ti-trash"></i> Hapus yang Dicentang</button>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;width:36px;"></th><th style="padding:10px;">Nama Siswa</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Keterangan</th><th style="padding:10px;text-align:right;">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($data as $d)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;"><input type="checkbox" name="ids[]" value="{{ $d->id }}" class="cb-item"></td>
                    <td style="padding:10px;font-weight:700;">{{ $d->siswa->nama_lengkap ?? '-' }}</td>
                    <td style="padding:10px;">{{ $d->siswa?->rombel ? "{$d->siswa->kelas}-{$d->siswa->rombel}" : $d->siswa?->kelas }}</td>
                    <td style="padding:10px;color:#64748b;">{{ $d->keterangan ?: '-' }}</td>
                    <td style="padding:10px;text-align:right;">
                        <button type="submit" formaction="{{ route('manajemen-sekolah.keterlambatan.hapus', $d) }}" formmethod="POST" onclick="return confirm('Hapus data keterlambatan {{ addslashes($d->siswa->nama_lengkap ?? '') }}?')" class="btn btn-secondary btn-sm" style="color:#dc2626;"><i class="ti ti-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa terlambat pada tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
{{ $data->links() }}
@endsection
