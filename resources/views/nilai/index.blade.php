@extends('layouts.app')
@section('title', 'Nilai Rapor — ' . $siswa->nama_lengkap)
@section('page-title', 'Nilai Rapor')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'nilai'])

@php
    $semuaMapel = \App\Models\NilaiRapor::daftarMapel();
    $nilaiGrid = [];
    foreach ($siswa->nilaiRapors as $n) $nilaiGrid[$n->kelas][$n->semester][$n->mata_pelajaran] = $n->nilai;
    $kolom = [
        ['label' => '7 / 1',  'kelas' => '7',  'sem' => 1],
        ['label' => '7 / 2',  'kelas' => '7',  'sem' => 2],
        ['label' => '8 / 1', 'kelas' => '8', 'sem' => 1],
        ['label' => '8 / 2', 'kelas' => '8', 'sem' => 2],
        ['label' => '9 / 1',   'kelas' => '9',   'sem' => 1],
        ['label' => '9 / 2',   'kelas' => '9',   'sem' => 2],
    ];
@endphp

<div class="card">
    <div class="card-header">
        <div>
            <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">{{ $siswa->nama_lengkap }}</p>
            <p style="font-size:12px;color:#64748b;margin:0;">NISN: {{ $siswa->nisn }} &nbsp;·&nbsp; Kelas {{ $siswa->rombel_lengkap }}</p>
        </div>
        <a href="{{ route('siswa.buku-induk.pdf', $siswa) }}" class="btn btn-secondary btn-sm"><i class="ti ti-printer"></i> Cetak Buku Induk</a>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:12px;">
            <thead>
                <tr>
                    <th style="padding:10px 14px;text-align:left;background:#0f1f45;color:#fff;font-size:11px;font-weight:700;width:28px;">No</th>
                    <th style="padding:10px 14px;text-align:left;background:#0f1f45;color:#fff;font-size:11px;font-weight:700;min-width:240px;">Mata Pelajaran</th>
                    @foreach($kolom as $k)
                    <th style="padding:10px 8px;text-align:center;background:#0f1f45;color:#fff;font-size:10px;font-weight:700;width:64px;line-height:1.4;">Kelas<br>{{ $k['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($semuaMapel as $idx => $mapel)
                <tr style="border-bottom:1px solid #f1f5f9;{{ $idx % 2 == 1 ? 'background:#fafafa;' : '' }}">
                    <td style="padding:8px 14px;color:#94a3b8;text-align:center;font-size:11px;">{{ $idx + 1 }}</td>
                    <td style="padding:8px 14px;color:#111827;font-weight:500;">{{ $mapel }}</td>
                    @foreach($kolom as $k)
                    @php $val = $nilaiGrid[$k['kelas']][$k['sem']][$mapel] ?? null; @endphp
                    <td style="padding:8px 6px;text-align:center;border-left:1px solid #f1f5f9;">
                        @if($val !== null)<span style="font-weight:800;font-size:13px;color:{{ $val >= 75 ? '#16a34a' : '#dc2626' }};">{{ number_format($val, 0) }}</span>
                        @else<span style="color:#e2e8f0;font-size:13px;">—</span>@endif
                    </td>
                    @endforeach
                </tr>
                @endforeach

                <tr style="border-top:2px solid #0f1f45;background:#eff6ff;">
                    <td colspan="2" style="padding:9px 14px;font-size:12px;font-weight:800;color:#0f1f45;">Rata-rata</td>
                    @foreach($kolom as $k)
                    @php
                        $vals = collect($siswa->nilaiRapors)->where('kelas', $k['kelas'])->where('semester', $k['sem'])->whereNotNull('nilai')->pluck('nilai');
                        $rata = $vals->count() > 0 ? $vals->avg() : null;
                    @endphp
                    <td style="padding:9px 6px;text-align:center;border-left:1px solid #dbeafe;">
                        @if($rata !== null)<span style="font-weight:800;font-size:13px;color:{{ $rata >= 75 ? '#16a34a' : '#dc2626' }};">{{ number_format($rata, 1) }}</span>
                        @else<span style="color:#e2e8f0;">—</span>@endif
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div style="padding:11px 16px;border-top:1px solid #f1f5f9;display:flex;align-items:center;gap:20px;">
        <span style="font-size:11px;color:#64748b;font-weight:600;">Keterangan:</span>
        <span style="font-size:11px;color:#16a34a;font-weight:700;">≥ 75 Tuntas</span>
        <span style="font-size:11px;color:#dc2626;font-weight:700;">&lt; 75 Belum Tuntas</span>
        <span style="font-size:11px;color:#d1d5db;">— Belum diisi</span>
    </div>
</div>
@endsection
