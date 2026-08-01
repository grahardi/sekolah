@extends('layouts.erapor')
@section('title', 'Tambah TP')
@section('page-title', 'Tambah Tujuan Pembelajaran')

@section('header-actions')
    <a href="{{ route('erapor.tp.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.tp.store') }}" method="POST" class="card" style="max-width:680px;margin:0 auto;padding:24px;">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
        <div>
            <label class="form-label">Mata Pelajaran <span style="color:#ef4444">*</span></label>
            <select name="mata_pelajaran_id" class="form-input" required>
                @foreach($mapelList as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tahun Ajaran <span style="color:#ef4444">*</span></label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}" {{ $t->is_aktif ? 'selected' : '' }}>{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Fase</label>
            <input type="text" name="fase" class="form-input" placeholder="mis. D" maxlength="5">
        </div>
        <div>
            <label class="form-label">Kode TP</label>
            <input type="text" name="kode_tp" class="form-input" placeholder="mis. 7.1">
        </div>
        <div>
            <label class="form-label">Semester <span style="color:#ef4444">*</span></label>
            <select name="semester" class="form-input" required>
                <option value="1">Ganjil</option>
                <option value="2">Genap</option>
            </select>
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Deskripsi TP <span style="color:#ef4444">*</span></label>
        <textarea name="deskripsi_tp" class="form-input" rows="3" placeholder="mis. Peserta didik mampu menjelaskan konsep operasi bilangan bulat" required></textarea>
    </div>

    <div style="margin-bottom:20px;">
        <label class="form-label">Berlaku untuk Kelas <span style="color:#ef4444">*</span></label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">
            @forelse($kelasList as $k)
            @php [$kl,$rb] = explode('|', $k); @endphp
            <label style="display:flex;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
                <input type="checkbox" name="kelas_rombel[]" value="{{ $k }}">
                {{ $rb ? "$kl - $rb" : $kl }}
            </label>
            @empty
            <p style="font-size:12px;color:#94a3b8;">Belum ada data kelas siswa aktif.</p>
            @endforelse
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan TP</button>
</form>
@endsection
