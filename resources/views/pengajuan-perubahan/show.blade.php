@extends('layouts.app')
@section('title', 'Review Pengajuan - ' . $siswa->nama_lengkap)
@section('page-title', 'Review Pengajuan Perubahan')

@section('header-actions')
    <a href="{{ route('pengajuan-perubahan.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:-6px 0 4px;">{{ $siswa->nama_lengkap }} &middot; {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
@if($pengajuan->diajukan_at)
<p style="font-size:12px;color:#94a3b8;margin:0 0 16px;">Diajukan {{ $pengajuan->diajukan_at->locale('id')->diffForHumans() }}</p>
@endif

@if($pengajuan->status !== 'menunggu_approval')
<div style="background:#f1f5f9;color:#64748b;padding:14px 16px;border-radius:8px;font-size:13px;">Tidak ada pengajuan yang menunggu approval untuk siswa ini.</div>
@else

@php
$labelField = [
    'nama_lengkap' => 'Nama Lengkap', 'nik' => 'NIK', 'tempat_lahir' => 'Tempat Lahir', 'tanggal_lahir' => 'Tanggal Lahir',
    'agama' => 'Agama', 'no_telepon' => 'No. Telepon', 'email' => 'Email',
    'alamat' => 'Alamat', 'rt' => 'RT', 'rw' => 'RW', 'dusun' => 'Dusun', 'kelurahan' => 'Kelurahan', 'kecamatan' => 'Kecamatan', 'kode_pos' => 'Kode Pos',
    'nama_ayah' => 'Nama Ayah', 'nik_ayah' => 'NIK Ayah', 'tahun_lahir_ayah' => 'Tahun Lahir Ayah', 'pendidikan_ayah' => 'Pendidikan Ayah', 'pekerjaan_ayah' => 'Pekerjaan Ayah', 'penghasilan_ayah' => 'Penghasilan Ayah',
    'nama_ibu' => 'Nama Ibu', 'nik_ibu' => 'NIK Ibu', 'tahun_lahir_ibu' => 'Tahun Lahir Ibu', 'pendidikan_ibu' => 'Pendidikan Ibu', 'pekerjaan_ibu' => 'Pekerjaan Ibu', 'penghasilan_ibu' => 'Penghasilan Ibu',
    'nama_wali' => 'Nama Wali', 'pekerjaan_wali' => 'Pekerjaan Wali', 'no_telepon_ortu' => 'No. Telepon Ortu/Wali', 'alamat_ortu' => 'Alamat Ortu/Wali',
];
@endphp

@if($pengajuan->catatan_siswa)
<div style="background:#eff6ff;color:#1e40af;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    <strong>Catatan dari siswa:</strong> {{ $pengajuan->catatan_siswa }}
</div>
@endif

<form action="{{ route('pengajuan-perubahan.proses', $siswa) }}" method="POST" id="form-proses">
    @csrf

    <div class="card" style="padding:0;overflow:hidden;margin-bottom:16px;">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:9px 12px;text-align:left;width:36px;"><input type="checkbox" id="cb-semua" onclick="document.querySelectorAll('.cb-terapkan').forEach(cb => cb.checked = this.checked)"></th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;width:170px;">Field</th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;">Data Induk (Sekarang)</th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#7c3aed;">Usulan Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengajuan->data_perubahan as $field => $nilaiBaru)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:9px 12px;"><input type="checkbox" name="fields[]" value="{{ $field }}" class="cb-terapkan" checked></td>
                    <td style="padding:9px 12px;font-size:12px;color:#64748b;">{{ $labelField[$field] ?? $field }}</td>
                    <td style="padding:9px 12px;font-size:13px;color:#0f172a;">{{ $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('d-m-Y') : ($siswa->{$field} ?: '-') }}</td>
                    <td style="padding:9px 12px;font-size:13px;font-weight:600;color:#7c3aed;">{{ $field === 'tanggal_lahir' ? \Carbon\Carbon::parse($nilaiBaru)->format('d-m-Y') : $nilaiBaru }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:12px 18px;border-top:1px solid #f1f5f9;">
            <label style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" onclick="document.getElementById('cb-semua').checked = this.checked; document.getElementById('cb-semua').click();"> Centang / lepas semua
            </label>
        </div>
    </div>

    <div class="card" style="padding:18px;background:#fffbeb;border-color:#fde68a;">
        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
            <input type="checkbox" name="yakin" value="1" id="cb-yakin" style="margin-top:2px;" onchange="document.getElementById('btn-simpan').disabled = !this.checked;">
            <span style="font-size:13px;color:#92400e;"><i class="ti ti-alert-triangle"></i> <strong>Saya yakin</strong> data yang dicentang di atas sudah benar dan siap diterapkan ke data induk siswa. Perubahan ini akan langsung berlaku setelah disimpan.</span>
        </label>
    </div>

    <div style="display:flex;justify-content:flex-end;margin-top:16px;">
        <button type="submit" id="btn-simpan" class="btn btn-primary" disabled><i class="ti ti-check"></i> Setujui & Simpan Perubahan</button>
    </div>
</form>
@endif

@endsection
