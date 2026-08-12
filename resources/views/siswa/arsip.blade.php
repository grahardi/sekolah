@extends('layouts.app')
@section('title', 'Arsip Berkas — ' . $siswa->nama_lengkap)
@section('page-title', 'Arsip Berkas Siswa')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'arsip'])

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

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

    @if($arsip->berkas_lain)
    <div class="card" style="margin-top:20px;">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-files" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#7c3aed;"></i> Berkas Lain</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;">
                @foreach($arsip->berkasLainUrls() as $b)
                @php $sudahAdaLabel = filled($b['label']); @endphp
                <div style="border:1px solid {{ $sudahAdaLabel ? '#c4b5fd' : '#e2e8f0' }};background:{{ $sudahAdaLabel ? '#f5f3ff' : '#fff' }};border-radius:10px;padding:10px;">
                    <a href="{{ $b['url'] }}" target="_blank" style="text-decoration:none;display:block;text-align:center;margin-bottom:8px;">
                        @if($b['is_image'])
                        <img src="{{ $b['url'] }}" style="width:100%;height:80px;object-fit:cover;border-radius:6px;">
                        @else
                        <div style="width:100%;height:80px;background:{{ $sudahAdaLabel ? '#ede9fe' : '#f1f5f9' }};border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="ti ti-file-text" style="font-size:24px;color:{{ $sudahAdaLabel ? '#7c3aed' : '#94a3b8' }};"></i>
                        </div>
                        @endif
                    </a>

                    @if($sudahAdaLabel)
                    <div id="tampil-label-{{ $b['index'] }}" style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:6px;">
                        <p style="font-size:12px;color:#5b21b6;font-weight:600;margin:0;word-break:break-word;"><i class="ti ti-tag" style="font-size:11px;"></i> {{ $b['label'] }}</p>
                        <button type="button" onclick="document.getElementById('tampil-label-{{ $b['index'] }}').style.display='none'; document.getElementById('form-label-{{ $b['index'] }}').style.display='flex';" class="btn btn-secondary btn-sm" style="flex-shrink:0;padding:3px 6px;" title="Edit keterangan">
                            <i class="ti ti-pencil" style="font-size:11px;"></i>
                        </button>
                    </div>
                    @else
                    <p style="font-size:10.5px;color:#94a3b8;margin:0 0 6px;word-break:break-all;">{{ \Illuminate\Support\Str::limit($b['nama_asli'], 24) }}</p>
                    @endif

                    <form id="form-label-{{ $b['index'] }}" action="{{ route('siswa.arsip.berkas-lain.label', $siswa) }}" method="POST" style="display:{{ $sudahAdaLabel ? 'none' : 'flex' }};gap:4px;margin-bottom:6px;">
                        @csrf
                        <input type="hidden" name="index" value="{{ $b['index'] }}">
                        <input type="text" name="label" value="{{ $b['label'] }}" placeholder="Jenis dokumen, mis. Sertifikat" class="form-input" style="font-size:11px;padding:5px 8px;flex:1;">
                        <button type="submit" class="btn btn-secondary btn-sm" title="Simpan keterangan"><i class="ti ti-check" style="font-size:12px;"></i></button>
                    </form>

                    <form action="{{ route('siswa.arsip.berkas-lain.pindah', $siswa) }}" method="POST" onsubmit="return confirm('Pindahkan berkas ini ke kategori terpilih?')">
                        @csrf
                        <input type="hidden" name="index" value="{{ $b['index'] }}">
                        <select name="field_tujuan" onchange="this.form.submit()" class="form-input" style="font-size:11px;padding:5px 6px;">
                            <option value="" disabled selected>Pindahkan ke...</option>
                            <option value="kartu_keluarga">Kartu Keluarga</option>
                            <option value="akta_lahir">Akta Lahir</option>
                            <option value="ijazah_sd">Ijazah SD/MI</option>
                            <option value="ijazah">Ijazah SMP</option>
                            <option value="transkrip_nilai">Transkrip Nilai</option>
                            <option value="sertifikat_tka">Sertifikat TKA</option>
                        </select>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

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
