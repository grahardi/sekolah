@extends('layouts.kepegawaian')
@section('title', 'Daftar Urut Kepangkatan')
@section('page-title', 'Daftar Urut Kepangkatan (DUK)')

@section('header-actions')
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Pegawai ASN aktif (PNS/PPPK), diurutkan berdasarkan golongan tertinggi lalu TMT pangkat terlama.
</p>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">No</th>
                    <th style="padding:10px;">Nama / NIP</th>
                    <th style="padding:10px;">Golongan</th>
                    <th style="padding:10px;">Pangkat</th>
                    <th style="padding:10px;">TMT Pangkat Terakhir</th>
                    <th style="padding:10px;">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawais as $i => $p)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:12px 18px; font-weight:700; color:#94a3b8;">{{ $i + 1 }}</td>
                    <td style="padding:12px 10px;">
                        <p style="font-weight:700;color:#0f172a;margin:0;">{{ $p->nama_lengkap }}</p>
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->nip_nuptk ?? '-' }}</p>
                    </td>
                    <td style="padding:12px 10px; font-weight:700; color:#1e40af;">{{ $p->golongan ?? '-' }}</td>
                    <td style="padding:12px 10px;">{{ $p->pangkat ?? '-' }}</td>
                    <td style="padding:12px 10px;">{{ $p->tmt_pangkat_terakhir?->format('d-m-Y') ?? '-' }}</td>
                    <td style="padding:12px 10px; color:#475569;">{{ $p->jabatan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada data pegawai ASN aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
