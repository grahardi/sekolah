@extends('layouts.kepegawaian')
@section('title', 'Kendali Pangkat')
@section('page-title', 'Kendali Pangkat')

@section('header-actions')
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Kenaikan pangkat reguler ASN terjadi setiap 4 tahun sejak TMT pangkat terakhir.
    Baris merah = sudah lewat jatuh tempo, kuning = jatuh tempo dalam 6 bulan ke depan.
</p>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">Nama / NIP</th>
                    <th style="padding:10px;">Golongan Saat Ini</th>
                    <th style="padding:10px;">TMT Pangkat Terakhir</th>
                    <th style="padding:10px;">Jatuh Tempo Berikutnya</th>
                    <th style="padding:10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $p)
                @php
                    $due = $p->jatuh_tempo_pangkat;
                    $monthsLeft = $due ? now()->diffInMonths($due, false) : null;
                    $status = $monthsLeft === null ? null : ($monthsLeft < 0 ? 'lewat' : ($monthsLeft <= 6 ? 'dekat' : 'aman'));
                @endphp
                <tr style="border-bottom:1px solid #f8fafc; {{ $status === 'lewat' ? 'background:#fef2f2;' : ($status === 'dekat' ? 'background:#fffbeb;' : '') }}">
                    <td style="padding:12px 18px;">
                        <p style="font-weight:700;color:#0f172a;margin:0;">{{ $p->nama_lengkap }}</p>
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->nip_nuptk ?? '-' }}</p>
                    </td>
                    <td style="padding:12px 10px; font-weight:700; color:#1e40af;">{{ $p->golongan ?? '-' }}</td>
                    <td style="padding:12px 10px;">{{ $p->tmt_pangkat_terakhir?->format('d-m-Y') ?? '-' }}</td>
                    <td style="padding:12px 10px; font-weight:600;">{{ $due?->format('d-m-Y') ?? '-' }}</td>
                    <td style="padding:12px 10px;">
                        @if($status === 'lewat')
                        <span class="badge badge-keluar">Lewat Tempo</span>
                        @elseif($status === 'dekat')
                        <span class="badge" style="background:#fef3c7;color:#92400e;">Segera ({{ abs($monthsLeft) }} bln)</span>
                        @elseif($status === 'aman')
                        <span class="badge badge-aktif">Aman</span>
                        @else
                        <span class="badge" style="background:#f1f5f9;color:#94a3b8;">TMT belum diisi</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada data TMT pangkat pegawai ASN.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
