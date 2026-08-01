@extends('layouts.erapor')
@section('title', 'Edit TP')
@section('page-title', 'Edit Tujuan Pembelajaran')

@section('header-actions')
    <a href="{{ route('erapor.tp.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.tp.update', $tp) }}" method="POST" class="card" style="max-width:680px;margin:0 auto;padding:24px;">
    @csrf
    @method('PUT')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
        <div>
            <label class="form-label">Mata Pelajaran <span style="color:#ef4444">*</span></label>
            <select name="mata_pelajaran_id" class="form-input" required>
                @foreach($mapelList as $m)<option value="{{ $m->id }}" {{ $tp->mata_pelajaran_id == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tahun Ajaran <span style="color:#ef4444">*</span></label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}" {{ $tp->tahun_ajaran_id == $t->id ? 'selected' : '' }}>{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Fase</label>
            <input type="text" name="fase" value="{{ $tp->fase }}" class="form-input" placeholder="mis. D" maxlength="5">
        </div>
        <div>
            <label class="form-label">Kode TP</label>
            <input type="text" name="kode_tp" value="{{ $tp->kode_tp }}" class="form-input" placeholder="mis. 7.1">
        </div>
        <div>
            <label class="form-label">Semester <span style="color:#ef4444">*</span></label>
            <select name="semester" class="form-input" required>
                <option value="1" {{ $tp->semester == 1 ? 'selected' : '' }}>Ganjil</option>
                <option value="2" {{ $tp->semester == 2 ? 'selected' : '' }}>Genap</option>
            </select>
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Deskripsi TP <span style="color:#ef4444">*</span></label>
        <textarea name="deskripsi_tp" class="form-input" rows="3" required>{{ $tp->deskripsi_tp }}</textarea>
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 12px;margin-top:8px;">
            <p style="font-size:11px;color:#1e40af;margin:0;">
                Mulai dengan kata kerja (mis. "Mengerjakan trigonometri"), bukan "Peserta didik mampu..." -
                karena disambung otomatis jadi kalimat capaian di rapor.
            </p>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
</form>
@endsection
