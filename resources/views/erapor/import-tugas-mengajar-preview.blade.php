@extends('layouts.erapor')
@section('title', 'Preview Import Tugas Mengajar')
@section('page-title', 'Preview Kecocokan')

@section('content')

@php
$jumlahPasti = collect($hasil)->where('status', 'pasti')->count();
$jumlahPerlu = collect($hasil)->where('status', 'perlu_konfirmasi')->count();
$jumlahTidak = collect($hasil)->where('status', 'tidak_ketemu')->count();
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:14px;margin-bottom:20px;">
    <div class="card" style="padding:16px;text-align:center;background:#f0fdf4;border-color:#bbf7d0;">
        <p style="font-size:26px;font-weight:800;color:#16a34a;margin:0;">{{ $jumlahPasti }}</p>
        <p style="font-size:11px;color:#166534;margin:4px 0 0;">Cocok 100% (otomatis)</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;background:#fffbeb;border-color:#fde68a;">
        <p style="font-size:26px;font-weight:800;color:#d97706;margin:0;">{{ $jumlahPerlu }}</p>
        <p style="font-size:11px;color:#92400e;margin:4px 0 0;">Perlu Dikonfirmasi</p>
    </div>
    <div class="card" style="padding:16px;text-align:center;background:#fef2f2;border-color:#fecaca;">
        <p style="font-size:26px;font-weight:800;color:#dc2626;margin:0;">{{ $jumlahTidak }}</p>
        <p style="font-size:11px;color:#991b1b;margin:4px 0 0;">Tidak Ketemu Sama Sekali</p>
    </div>
</div>

<form action="{{ route('erapor.tugas-mengajar.import') }}" method="POST" id="form-konfirmasi">
    @csrf
    <input type="hidden" name="path_tersimpan" value="{{ $pathTersimpan }}">
    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaranId }}">

    @if($jumlahPerlu > 0)
    <div style="margin-bottom:12px;">
        <button type="button" onclick="document.querySelectorAll('input[name=\'konfirmasi[]\']').forEach(cb => cb.checked = true)" class="btn btn-secondary btn-sm">Centang Semua</button>
        <button type="button" onclick="document.querySelectorAll('input[name=\'konfirmasi[]\']').forEach(cb => cb.checked = false)" class="btn btn-secondary btn-sm">Lepas Semua</button>
    </div>
    @endif

    <div class="card" style="padding:0;overflow:hidden;margin-bottom:20px;">
        <div style="padding:10px 16px;background:#f8fafc;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" id="centang-semua" onclick="document.querySelectorAll('input[name=\'konfirmasi[]\']').forEach(cb => cb.checked = this.checked)">
            <label for="centang-semua" style="font-size:12px;font-weight:600;color:#374151;cursor:pointer;">Centang Semua (baris kuning / perlu konfirmasi)</label>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:8px 12px;text-align:center;font-size:11px;color:#64748b;width:36px;"></th>
                    <th style="padding:8px 12px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                    <th style="padding:8px 12px;text-align:left;font-size:11px;color:#64748b;">Mapel</th>
                    <th style="padding:8px 12px;text-align:left;font-size:11px;color:#64748b;">Nama di Excel</th>
                    <th style="padding:8px 12px;text-align:left;font-size:11px;color:#64748b;">Guru Tercocokkan</th>
                    <th style="padding:8px 12px;text-align:center;font-size:11px;color:#64748b;">Persen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil as $h)
                <tr style="border-top:1px solid #f1f5f9;background:{{ $h['status'] === 'tidak_ketemu' ? '#fef2f2' : ($h['status'] === 'perlu_konfirmasi' ? '#fffbeb' : '#fff') }};">
                    <td style="padding:8px 12px;text-align:center;">
                        @if($h['status'] === 'perlu_konfirmasi')
                        <input type="checkbox" name="konfirmasi[]" value="{{ $h['row'] }}">
                        @elseif($h['status'] === 'pasti')
                        <i class="ti ti-check" style="color:#16a34a;"></i>
                        @else
                        <i class="ti ti-x" style="color:#dc2626;"></i>
                        @endif
                    </td>
                    <td style="padding:8px 12px;color:#334155;">{{ $h['kelas'] }}{{ $h['rombel'] ? "-{$h['rombel']}" : '' }}</td>
                    <td style="padding:8px 12px;color:#334155;">
                        {{ $h['mapel_nama'] }}
                        @if($h['mapel_baru'])<span style="font-size:10px;color:#1d4ed8;background:#eff6ff;padding:1px 6px;border-radius:10px;margin-left:4px;">baru</span>@endif
                    </td>
                    <td style="padding:8px 12px;color:#0f172a;font-weight:600;">{{ $h['guru_nama_excel'] }}</td>
                    <td style="padding:8px 12px;color:#334155;">{{ $h['guru_nama_cocok'] ?? '-' }}</td>
                    <td style="padding:8px 12px;text-align:center;font-weight:700;color:{{ $h['persen'] >= 100 ? '#16a34a' : ($h['persen'] >= 40 ? '#d97706' : '#dc2626') }};">{{ $h['persen'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="font-size:12px;color:#64748b;margin:0 0 14px;">
        Baris <strong>hijau (100%)</strong> otomatis diproses. Baris <strong>kuning</strong> cuma diproses kalau kamu centang. Baris <strong>merah</strong> (tidak ketemu sama sekali) selalu dilewati.
    </p>

    <a href="{{ route('erapor.tugas-mengajar.import-form') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Batal</a>
    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Konfirmasi & Import</button>
</form>

@endsection
