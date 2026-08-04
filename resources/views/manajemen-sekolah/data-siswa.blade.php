@extends('layouts.manajemen-sekolah')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')

@section('content')
<p style="font-size:12px;color:#94a3b8;margin:-10px 0 16px;">
    Data ini sama dengan Buku Induk Siswa - untuk tambah/edit data siswa, buka menu <a href="/buku-induk" style="color:#2563EB;">Buku Induk</a>.
</p>

<form method="GET" style="margin-bottom:18px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">NIS/NISN</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Jenis Kelamin</th>
        </tr></thead>
        <tbody>
            @forelse($siswaList as $s)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $s->nama_lengkap }}</td>
                <td style="padding:10px;font-family:monospace;color:#64748b;">{{ $s->nis }} / {{ $s->nisn }}</td>
                <td style="padding:10px;">{{ $s->rombel ? "{$s->kelas} - {$s->rombel}" : $s->kelas }}</td>
                <td style="padding:10px;">{{ $s->jenis_kelamin }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada data siswa.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $siswaList->links() }}
@endsection
