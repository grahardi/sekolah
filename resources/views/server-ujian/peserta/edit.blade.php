@extends('layouts.server-ujian')
@section('title', 'Edit Peserta')
@section('page-title', 'Edit Peserta: ' . $peserta->nama)

@section('content')

@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:560px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<a href="{{ route('server-ujian.peserta.index') }}" style="font-size:12px;color:#64748b;text-decoration:none;display:inline-block;margin-bottom:14px;">&larr; Kembali ke Daftar Peserta</a>

<div class="card" style="padding:22px;max-width:560px;">
    <form action="{{ route('server-ujian.peserta.update', $peserta->id) }}" method="POST">
        @csrf @method('PUT')

        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" value="{{ old('nama', $peserta->nama) }}" class="form-input" required style="margin-bottom:14px;">

        <label class="form-label">No. Ujian (tidak bisa diubah)</label>
        <input type="text" value="{{ $peserta->no_ujian }}" class="form-input" disabled style="margin-bottom:14px;background:#f8fafc;">

        <label class="form-label">Kelas / Kelompok</label>
        <select name="group_id" class="form-input" style="margin-bottom:14px;">
            <option value="">-- Tidak masuk kelompok manapun --</option>
            @foreach($groupList as $g)
            <option value="{{ $g->id }}" {{ ($groupSaatIni->id ?? null) === $g->id ? 'selected' : '' }}>{{ $g->parent_name }} - {{ $g->name }}</option>
            @endforeach
        </select>

        <label class="form-label">Agama</label>
        <select name="agama_id" class="form-input" required style="margin-bottom:14px;">
            @foreach($agamaList as $a)
            <option value="{{ $a->id }}" {{ $peserta->agama_id === $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
            @endforeach
        </select>

        <label class="form-label">Status</label>
        <select name="status" class="form-input" style="margin-bottom:14px;">
            <option value="1" {{ $peserta->status == 1 ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ $peserta->status == 0 ? 'selected' : '' }}>Terblokir</option>
        </select>
        <p style="font-size:11px;color:#94a3b8;margin:-10px 0 14px;">Pilih "Aktif" untuk otomatis membuka blokir peserta ini (kalau sebelumnya terblokir).</p>

        <label class="form-label">Reset Password (opsional)</label>
        <input type="text" name="password_baru" class="form-input" placeholder="Kosongkan kalau tidak ingin diubah" style="margin-bottom:20px;">

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
            <a href="{{ route('server-ujian.peserta.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection
