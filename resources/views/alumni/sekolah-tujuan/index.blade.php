@extends('layouts.alumni')
@section('title', 'Sekolah Tujuan')
@section('page-title', 'Daftar Sekolah Tujuan')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Kelola daftar sekolah pilihan yang muncul di form pengisian data alumni (biasanya sekolah lanjutan yang paling banyak dipilih/terdekat). Alumni tetap bisa isi manual kalau sekolahnya tidak ada di daftar.
</p>

<form action="{{ route('alumni.sekolah-tujuan.store') }}" method="POST" style="display:flex;gap:8px;margin-bottom:20px;max-width:600px;">
    @csrf
    <input type="text" name="nama_sekolah" class="form-input" placeholder="Nama sekolah" required style="flex:2;">
    <input type="text" name="jenjang" class="form-input" placeholder="Jenjang (SMA/SMK)" style="flex:1;">
    <button type="submit" class="btn btn-primary"><i class="ti ti-plus"></i> Tambah</button>
</form>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Sekolah</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Jenjang</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftar as $st)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $st->nama_sekolah }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $st->jenjang ?: '-' }}</td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $st->aktif ? '#dcfce7' : '#f1f5f9' }};color:{{ $st->aktif ? '#166534' : '#94a3b8' }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $st->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    <button type="button" onclick="document.getElementById('modal-edit-{{ $st->id }}').style.display='flex'" class="btn btn-secondary btn-sm">Edit</button>
                    <form action="{{ route('alumni.sekolah-tujuan.update', $st) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="nama_sekolah" value="{{ $st->nama_sekolah }}">
                        <input type="hidden" name="jenjang" value="{{ $st->jenjang }}">
                        <input type="hidden" name="aktif" value="{{ $st->aktif ? 0 : 1 }}">
                        <button type="submit" class="btn btn-secondary btn-sm">{{ $st->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <form action="{{ route('alumni.sekolah-tujuan.destroy', $st) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus {{ addslashes($st->nama_sekolah) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada sekolah tujuan. Tambahkan lewat form di atas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach($daftar as $st)
<div id="modal-edit-{{ $st->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Edit Sekolah Tujuan</p>
            <button type="button" onclick="document.getElementById('modal-edit-{{ $st->id }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('alumni.sekolah-tujuan.update', $st) }}" method="POST">
            @csrf @method('PUT')
            <label class="form-label">Nama Sekolah</label>
            <input type="text" name="nama_sekolah" value="{{ $st->nama_sekolah }}" class="form-input" required style="margin-bottom:12px;">
            <label class="form-label">Jenjang</label>
            <input type="text" name="jenjang" value="{{ $st->jenjang }}" class="form-input" placeholder="SMA/SMK" style="margin-bottom:16px;">
            <input type="hidden" name="aktif" value="{{ $st->aktif ? 1 : 0 }}">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endforeach

@endsection
