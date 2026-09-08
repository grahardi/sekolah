@extends('layouts.pengguna')
@section('title', $role ? 'Edit Role' : 'Tambah Role')
@section('page-title', $role ? "Edit Role: {$role->nama}" : 'Tambah Role Baru')

@section('header-actions')
    <a href="{{ route('role.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form action="{{ $role ? route('role.update', $role) : route('role.store') }}" method="POST">
    @csrf
    @if($role) @method('PUT') @endif

    <div class="card" style="padding:20px;max-width:600px;margin-bottom:16px;">
        <label class="form-label">Nama Role</label>
        <input type="text" name="nama" value="{{ old('nama', $role->nama ?? '') }}" class="form-input" required placeholder="mis. Kurikulum, Bendahara">
    </div>

    <div class="card" style="padding:0;overflow:hidden;max-width:600px;">
        <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">Akses Modul</p>
            <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Centang "Boleh Akses" untuk modul yg mau diizinkan, lalu pilih Baca Saja atau Bisa Ubah.</p>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:8px 16px;text-align:left;font-size:11px;color:#64748b;">Modul</th>
                    <th style="padding:8px 16px;text-align:center;font-size:11px;color:#64748b;">Boleh Akses</th>
                    <th style="padding:8px 16px;text-align:center;font-size:11px;color:#64748b;">Baca Saja</th>
                    <th style="padding:8px 16px;text-align:center;font-size:11px;color:#64748b;">Bisa Ubah</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\CustomRole::DAFTAR_MODUL as $key => $label)
                @php $p = $permissionMap[$key]; @endphp
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:10px 16px;font-size:13px;color:#0f172a;">{{ $label }}</td>
                    <td style="padding:10px 16px;text-align:center;">
                        <input type="checkbox" name="modul[{{ $key }}][boleh_akses]" value="1" {{ $p['boleh_akses'] ? 'checked' : '' }}>
                    </td>
                    <td style="padding:10px 16px;text-align:center;">
                        <input type="radio" name="modul[{{ $key }}][read_only]" value="1" {{ $p['read_only'] ? 'checked' : '' }}>
                    </td>
                    <td style="padding:10px 16px;text-align:center;">
                        <input type="radio" name="modul[{{ $key }}][read_only]" value="0" {{ ! $p['read_only'] ? 'checked' : '' }}>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;max-width:600px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Role</button>
    </div>
</form>

@endsection
