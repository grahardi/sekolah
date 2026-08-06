@extends('layouts.erapor')
@section('title', 'Edit Penilaian')

@section('content')
<div style="max-width:600px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 4px;">Edit Penilaian</h2>
    <p style="font-size:13px;color:#64748b;margin:0 0 20px;">
        {{ $penilaian->mataPelajaran->nama }} &middot; Kelas {{ $penilaian->rombel ? "{$penilaian->kelas} - {$penilaian->rombel}" : $penilaian->kelas }} &middot;
        {{ $penilaian->subjenis_penilaian }}
    </p>

    @if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('erapor.penilaian.update', $penilaian) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom:16px;">
            <label class="form-label">Nama Penilaian <span style="color:#ef4444">*</span></label>
            <input type="text" name="nama_penilaian" value="{{ old('nama_penilaian', $penilaian->nama_penilaian) }}" class="form-input" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
            <div>
                <label class="form-label">Bobot <span style="color:#ef4444">*</span></label>
                <input type="number" name="bobot_penilaian" value="{{ old('bobot_penilaian', $penilaian->bobot_penilaian) }}" class="form-input" min="1" max="100" required>
            </div>
            <div>
                <label class="form-label">Tanggal Penilaian</label>
                <input type="date" name="tanggal_penilaian" value="{{ old('tanggal_penilaian', $penilaian->tanggal_penilaian?->toDateString()) }}" class="form-input">
            </div>
        </div>

        @if($penilaian->subjenis_penilaian === 'Sumatif TP')
        <div style="margin-bottom:20px;">
            <label class="form-label">Tujuan Pembelajaran yang Diuji</label>
            <div style="display:flex;flex-direction:column;gap:6px;margin-top:6px;">
                @forelse($tpList as $tp)
                <label style="display:flex;align-items:flex-start;gap:8px;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;">
                    <input type="checkbox" name="tp_ids[]" value="{{ $tp->id }}" {{ in_array($tp->id, old('tp_ids', $tpTerpilih)) ? 'checked' : '' }} style="margin-top:2px;">
                    <span><strong>{{ $tp->kode_tp }}</strong> - {{ $tp->deskripsi_tp }}</span>
                </label>
                @empty
                <p style="font-size:12px;color:#94a3b8;">Tidak ada TP yang cocok utk mapel/kelas/semester ini.</p>
                @endforelse
            </div>
        </div>
        @endif

        <div style="display:flex;gap:10px;">
            <a href="{{ route('erapor.penilaian.show', $penilaian) }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
