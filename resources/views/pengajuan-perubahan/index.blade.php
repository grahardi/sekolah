@extends('layouts.app')
@section('title', 'Ajuan Perubahan Data')
@section('page-title', 'Ajuan Perubahan Data Siswa')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Siswa/orang tua bisa mengajukan perubahan data lewat tautan khusus + token. Kamu tinggal review & setujui perubahan yang masuk di sini.
</p>

<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $s)
            @php
            $pengajuan = $s->pengajuanPerubahan;
            $status = $pengajuan?->status ?? 'belum_isi';
            $warna = ['belum_isi' => ['bg' => '#f1f5f9', 'txt' => '#64748b'], 'menunggu_approval' => ['bg' => '#fef9c3', 'txt' => '#854d0e'], 'sudah_approve' => ['bg' => '#dcfce7', 'txt' => '#166534']][$status];
            $label = ['belum_isi' => 'Belum Mengisi', 'menunggu_approval' => 'Menunggu Approval', 'sudah_approve' => 'Sudah Approve'][$status];
            @endphp
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;font-size:13px;color:#0f172a;font-weight:500;">{{ $s->nama_lengkap }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $s->kelas }}{{ $s->rombel ? " - $s->rombel" : '' }}</td>
                <td style="padding:10px 16px;">
                    <span style="background:{{ $warna['bg'] }};color:{{ $warna['txt'] }};font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">{{ $label }}</span>
                </td>
                <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                    @if($status === 'menunggu_approval')
                    <a href="{{ route('pengajuan-perubahan.show', $s) }}" class="btn btn-primary btn-sm">Review</a>
                    @else
                    <button type="button" onclick="document.getElementById('modal-token-{{ $s->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-link"></i> Tautan</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada siswa di kelas yang Anda wali-i.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach($siswaList as $s)
@php $sekolah = auth()->user()->sekolah; $pengajuan = \App\Models\PengajuanPerubahan::buatAtauAmbilUntuk($s); @endphp
<div id="modal-token-{{ $s->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Tautan Pengajuan - {{ $s->nama_lengkap }}</p>
            <button type="button" onclick="document.getElementById('modal-token-{{ $s->id }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <label class="form-label">Tautan</label>
        <input type="text" readonly value="{{ url("/{$sekolah->npsn}/siswa/{$s->kode_akses}/pengajuan") }}" class="form-input" style="font-size:11px;margin-bottom:10px;" onclick="this.select()">
        <label class="form-label">Token (bagikan bareng tautan)</label>
        <input type="text" readonly value="{{ $pengajuan->token }}" class="form-input" style="font-family:monospace;font-weight:700;margin-bottom:14px;" onclick="this.select()">
        <form action="{{ route('pengajuan-perubahan.token-baru', $s) }}" method="POST" onsubmit="return confirm('Buat token baru? Token lama gak bisa dipakai lagi.')">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-refresh"></i> Buat Token Baru</button>
        </form>
    </div>
</div>
@endforeach

@endsection
