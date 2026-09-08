@extends('layouts.pengguna')
@section('title', 'Manajemen Role')
@section('page-title', 'Manajemen Role')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Buat role custom (mis. "Kurikulum", "Bendahara") dengan akses terbatas ke modul tertentu saja, baca-saja atau bisa ubah data.
</p>

<a href="{{ route('role.create') }}" class="btn btn-primary" style="margin-bottom:16px;"><i class="ti ti-plus"></i> Tambah Role Baru</a>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Role</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Jumlah User</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $r)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $r->nama }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $r->users_count }} user</td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    <a href="{{ route('role.edit', $r) }}" class="btn btn-secondary btn-sm">Edit</a>
                    <form action="{{ route('role.destroy', $r) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus role {{ addslashes($r->nama) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada role custom. Tambahkan lewat tombol di atas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
