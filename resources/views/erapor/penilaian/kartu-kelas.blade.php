@extends('layouts.erapor')
@section('title', 'Input Nilai')
@section('page-title', 'Penilaian & Input Nilai')

@section('header-actions')
    <a href="{{ route('erapor.penilaian.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Buat Penilaian</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Pilih kelas &amp; mapel untuk lihat rekap penilaian dan nilai akhir.
</p>

@php
    $palet = ['#2563EB', '#7C3AED', '#DB2777', '#EA580C', '#16A34A', '#0891B2', '#4F46E5', '#0D9488', '#C026D3', '#CA8A04'];
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
    @forelse($kombinasi as $i => $k)
    @php $warna = $palet[$i % count($palet)]; @endphp
    <a href="{{ route('erapor.penilaian.index', ['mata_pelajaran_id' => $k['mapel_id'], 'kelas_rombel' => $k['kelas_rombel']]) }}" style="text-decoration:none;">
        <div class="card" style="overflow:hidden;padding:0;height:100%;">
            <div style="background:{{ $warna }};padding:16px 18px;color:#fff;">
                <p style="font-size:15px;font-weight:800;margin:0 0 2px;">{{ $k['mapel_nama'] }}</p>
                <p style="font-size:11px;opacity:.85;margin:0;">Kelas {{ $k['rombel'] ? "{$k['kelas']} - {$k['rombel']}" : $k['kelas'] }}</p>
            </div>
            <div style="padding:16px 18px;display:flex;gap:20px;">
                <div>
                    <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">{{ $k['jumlah_tp'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">Tujuan Pembelajaran</p>
                </div>
                <div>
                    <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">{{ $k['jumlah_penilaian'] }}</p>
                    <p style="font-size:11px;color:#94a3b8;margin:0;">Penilaian Dibuat</p>
                </div>
            </div>
        </div>
    </a>
    @empty
    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:30px;">
        @if(auth()->user()->role === 'guru')
        Belum ada penugasan mengajar yang ditetapkan admin. Hubungi admin sekolah.
        @else
        Belum ada penugasan Guru Pengajar. Atur dulu di menu Wali Kelas &amp; Lainnya.
        @endif
    </p>
    @endforelse
</div>
@endsection
