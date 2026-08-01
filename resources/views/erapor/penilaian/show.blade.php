@extends('layouts.erapor')
@section('title', 'Input Nilai - ' . $penilaian->nama_penilaian)
@section('page-title', $penilaian->nama_penilaian)

@section('header-actions')
    <a href="{{ route('erapor.penilaian.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="card" style="padding:16px;margin-bottom:16px;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
    <span class="badge" style="background:{{ $penilaian->jenis_penilaian === 'Sumatif' ? '#fef3c7' : '#eff6ff' }};color:{{ $penilaian->jenis_penilaian === 'Sumatif' ? '#92400e' : '#2563EB' }};">
        {{ $penilaian->subjenis_label }}
    </span>
    <span style="font-size:13px;color:#64748b;">{{ $penilaian->mataPelajaran->nama }} &middot; {{ $penilaian->kelas_lengkap }} &middot; Bobot {{ $penilaian->bobot_penilaian }}</span>
    @if($penilaian->tujuanPembelajarans->count() > 0)
    <span style="font-size:11px;color:#94a3b8;">TP: {{ $penilaian->tujuanPembelajarans->pluck('kode_tp')->filter()->implode(', ') ?: $penilaian->tujuanPembelajarans->count() . ' TP' }}</span>
    @endif
</div>

<form action="{{ route('erapor.penilaian.save-nilai', $penilaian) }}" method="POST" class="card">
    @csrf
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">No</th><th style="padding:10px;">Nama Siswa</th><th style="padding:10px;">NISN</th><th style="padding:10px 18px;width:140px;">Nilai (0-100)</th>
        </tr></thead>
        <tbody>
            @forelse($siswaList as $i => $siswa)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="padding:10px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                <td style="padding:10px;color:#94a3b8;font-family:monospace;">{{ $siswa->nisn }}</td>
                <td style="padding:10px 18px;">
                    <input type="number" name="nilai[{{ $siswa->id }}]" class="form-input" min="0" max="100"
                           value="{{ $nilaiExisting[$siswa->id] ?? '' }}" style="text-align:center;">
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($siswaList->count() > 0)
    <div style="padding:16px 18px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Semua Nilai</button>
    </div>
    @endif
</form>
@endsection
