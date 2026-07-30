@extends('layouts.app')
@section('title', 'Arsip Berkas — ' . $siswa->nama_lengkap)
@section('page-title', 'Arsip Berkas Siswa')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'arsip'])

<form action="{{ route('siswa.arsip.update', $siswa) }}" method="POST" enctype="multipart/form-data" id="form-arsip">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-folder" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Berkas Masuk / Aktif</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:20px;">
                @foreach(\App\Models\ArsipBerkas::berkasAktif() as $field => $meta)
                    @include('siswa._arsip-item', ['field' => $field, 'meta' => $meta, 'arsip' => $arsip])
                @endforeach
            </div>
        </div>
        <div class="card" style="{{ $siswa->status !== 'lulus' ? 'opacity:.65;' : '' }}">
            <div class="card-header">
                <span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-folder-check" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#16a34a;"></i> Berkas Kelulusan</span>
                @if($siswa->status !== 'lulus')<span style="font-size:11px;color:#94a3b8;">Hanya untuk siswa lulus</span>@endif
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:20px;">
                @foreach(\App\Models\ArsipBerkas::berkasLulus() as $field => $meta)
                    @include('siswa._arsip-item', ['field' => $field, 'meta' => $meta, 'arsip' => $arsip])
                @endforeach
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="card" style="margin-top:20px;">
        <div class="card-body">
            <label class="form-label">Catatan Arsip</label>
            <textarea name="catatan" rows="2" class="form-input" placeholder="Catatan tambahan...">{{ $arsip->catatan }}</textarea>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;margin-top:16px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Arsip</button>
    </div>
    @else
    @if($arsip->catatan)
    <div class="card" style="margin-top:20px;">
        <div class="card-body">
            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0 0 6px;">Catatan Arsip</p>
            <p style="font-size:13px;color:#374151;margin:0;">{{ $arsip->catatan }}</p>
        </div>
    </div>
    @endif
    @endif
</form>

<form action="{{ route('siswa.arsip.hapus', $siswa) }}" method="POST" id="form-hapus-berkas" style="display:none;">
    @csrf
    <input type="hidden" name="field" id="hapus-field-input">
</form>
@endsection

@push('scripts')
<script>
function showFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (label) label.textContent = input.files[0]?.name ?? '';
}
function hapusBerkas(e, el, field, label) {
    e.preventDefault();
    if (!confirm('Hapus berkas "' + label + '"?')) return;
    document.getElementById('hapus-field-input').value = field;
    document.getElementById('form-hapus-berkas').submit();
}
</script>
@endpush
