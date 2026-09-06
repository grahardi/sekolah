@extends('layouts.alumni')
@section('title', 'Ajuan Ulang Alumni')
@section('page-title', 'Ajuan Ulang Alumni')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Alumni yang datanya sudah pernah diisi (locked) hanya bisa mengubah lewat pengajuan ini - butuh persetujuan admin sekolah.
</p>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama Alumni</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Diajukan</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Data Awal</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#7c3aed;">Data Perubahan</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftar as $a)
            @php
            $warna = ['menunggu' => ['bg' => '#fef9c3', 'txt' => '#854d0e'], 'disetujui' => ['bg' => '#dcfce7', 'txt' => '#166534'], 'ditolak' => ['bg' => '#fef2f2', 'txt' => '#991b1b']][$a->status];
            $label = ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'][$a->status];
            @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $a->siswa->nama_lengkap ?? '-' }}</td>
                <td style="padding:10px 16px;font-size:12px;color:#94a3b8;">{{ $a->created_at->locale('id')->diffForHumans() }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">
                    {{ $a->siswa?->alumni_diisi_at ? $a->siswa->alumni_label : 'Belum Pernah Mengisi' }}
                    @if($a->siswa?->alumni_kategori === 'lanjut_sekolah' && $a->siswa?->alumni_jurusan)
                    <br><span style="font-size:11px;color:#94a3b8;">Jurusan: {{ $a->siswa->alumni_jurusan }}</span>
                    @endif
                </td>
                <td style="padding:10px 16px;font-size:13px;color:#7c3aed;font-weight:600;">
                    {{ $a->labelTujuan() }}
                    @if($a->alumni_kategori === 'lanjut_sekolah' && $a->alumni_jurusan)
                    <br><span style="font-size:11px;color:#a78bfa;font-weight:400;">Jurusan: {{ $a->alumni_jurusan }}</span>
                    @endif
                </td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $label }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    @if($a->status === 'menunggu')
                    <form action="{{ route('alumni.ajuan-ulang.proses', $a) }}" method="POST" style="display:inline;" onsubmit="return confirm('Setujui perubahan ini? Data alumni akan langsung diperbarui.')">
                        @csrf
                        <input type="hidden" name="aksi" value="setuju">
                        <button type="submit" class="btn btn-primary btn-sm">Setujui</button>
                    </form>
                    <form action="{{ route('alumni.ajuan-ulang.proses', $a) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tolak pengajuan ini?')">
                        @csrf
                        <input type="hidden" name="aksi" value="tolak">
                        <button type="submit" class="btn btn-secondary btn-sm" style="color:#dc2626;">Tolak</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada pengajuan perubahan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $daftar->links() }}</div>

@endsection
