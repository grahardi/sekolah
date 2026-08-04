@extends('layouts.manajemen-sekolah')
@section('title', 'Data Siswa')

@section('content')
<h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Data Siswa</h2>
<p style="font-size:12px;color:#94a3b8;margin:0 0 16px;">
    Data ini sama dengan Buku Induk Siswa - untuk tambah/edit data siswa, buka menu <a href="/buku-induk" style="color:#2563EB;">Buku Induk</a>.
</p>

<form method="GET" style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa..." class="form-input" style="max-width:280px;">
    <select name="kelas_rombel" class="form-input" style="max-width:160px;" onchange="this.form.submit()">
        <option value="">-- Semua kelas --</option>
        @foreach($daftarKelas as $k)
        @php [$kl,$rb]=explode('|',$k); @endphp
        <option value="{{ $k }}" {{ $kelasRombelFilter === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

<div style="display:flex;gap:14px;margin-bottom:14px;font-size:11px;color:#64748b;">
    <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:#dcfce7;display:inline-block;"></span> Laki-laki</span>
    <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:#fce7f3;display:inline-block;"></span> Perempuan</span>
</div>

<div style="display:flex;flex-direction:column;gap:10px;">
    @forelse($siswaList as $i => $s)
    <div style="display:flex;align-items:center;justify-content:space-between;background:{{ $s->jenis_kelamin === 'L' ? '#f0fdf4' : '#fdf2f8' }};border-radius:12px;padding:14px 20px;box-shadow:0 1px 2px rgba(0,0,0,.03);">
        <div style="display:flex;align-items:center;gap:16px;">
            <span style="color:{{ $s->jenis_kelamin === 'L' ? '#86efac' : '#f9a8d4' }};font-weight:700;font-size:13px;min-width:20px;">{{ $siswaList->firstItem() + $i }}</span>
            <span style="color:{{ $s->jenis_kelamin === 'L' ? '#86efac' : '#f9a8d4' }};font-family:monospace;font-size:13px;">{{ $s->nis }}</span>
            <span style="color:{{ $s->jenis_kelamin === 'L' ? '#166534' : '#be185d' }};font-weight:800;font-size:14px;">{{ $s->nama_lengkap }}</span>
        </div>
        <span style="color:{{ $s->jenis_kelamin === 'L' ? '#166534' : '#be185d' }};font-weight:700;font-size:13px;">{{ $s->rombel ? "{$s->kelas} - {$s->rombel}" : $s->kelas }}</span>
    </div>
    @empty
    <p style="text-align:center;color:#94a3b8;padding:30px;">Tidak ada data siswa.</p>
    @endforelse
</div>
<div style="margin-top:16px;">{{ $siswaList->links() }}</div>
@endsection
