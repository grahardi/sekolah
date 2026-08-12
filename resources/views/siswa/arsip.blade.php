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
            @php
            $paletWarna = [
                ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'icon' => '#2563eb', 'text' => '#1e40af'],
                ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'icon' => '#16a34a', 'text' => '#166534'],
                ['bg' => '#fefce8', 'border' => '#fde68a', 'icon' => '#ca8a04', 'text' => '#854d0e'],
                ['bg' => '#fdf2f8', 'border' => '#fbcfe8', 'icon' => '#db2777', 'text' => '#9d174d'],
                ['bg' => '#f5f3ff', 'border' => '#ddd6fe', 'icon' => '#7c3aed', 'text' => '#5b21b6'],
                ['bg' => '#ecfeff', 'border' => '#a5f3fc', 'icon' => '#0891b2', 'text' => '#155e75'],
                ['bg' => '#fff7ed', 'border' => '#fed7aa', 'icon' => '#ea580c', 'text' => '#9a3412'],
            ];
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;">
                @foreach($arsip->berkasLainUrls() as $b)
                @php
                $sudahAdaLabel = filled($b['label']);
                $warna = $paletWarna[$b['index'] % count($paletWarna)];
                @endphp
                <button type="button" onclick="document.getElementById('modal-berkas-lain-{{ $b['index'] }}').style.display='flex'"
                    style="border:1px solid {{ $sudahAdaLabel ? $warna['border'] : '#e2e8f0' }};background:{{ $sudahAdaLabel ? $warna['bg'] : '#fff' }};border-radius:10px;padding:12px 10px;text-align:center;cursor:pointer;">
                    @if($b['is_image'])
                    <img src="{{ $b['url'] }}" style="width:100%;height:60px;object-fit:cover;border-radius:6px;margin-bottom:6px;">
                    @else
                    <div style="width:100%;height:60px;background:{{ $sudahAdaLabel ? $warna['border'] : '#f1f5f9' }};border-radius:6px;display:flex;align-items:center;justify-content:center;margin-bottom:6px;">
                        <i class="ti ti-file-text" style="font-size:22px;color:{{ $sudahAdaLabel ? $warna['icon'] : '#94a3b8' }};"></i>
                    </div>
                    @endif
                    <p style="font-size:11px;margin:0;word-break:break-word;{{ $sudahAdaLabel ? "color:{$warna['text']};font-weight:600;" : 'color:#94a3b8;' }}">
                        @if($sudahAdaLabel)<i class="ti ti-tag" style="font-size:10px;"></i> {{ $b['label'] }}
                        @else {{ \Illuminate\Support\Str::limit($b['nama_asli'], 18) }}
                        @endif
                    </p>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal per berkas: preview + edit label + pindahkan --}}
    @foreach($arsip->berkasLainUrls() as $b)
    @php $warnaModal = $paletWarna[$b['index'] % count($paletWarna)]; @endphp
    <div id="modal-berkas-lain-{{ $b['index'] }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
        <div class="card" style="max-width:380px;width:100%;padding:22px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Detail Berkas</p>
                <button type="button" onclick="document.getElementById('modal-berkas-lain-{{ $b['index'] }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;line-height:1;">&times;</button>
            </div>

            <a href="{{ $b['url'] }}" target="_blank" style="text-decoration:none;display:block;text-align:center;margin-bottom:14px;">
                @if($b['is_image'])
                <img src="{{ $b['url'] }}" style="width:100%;max-height:220px;object-fit:contain;border-radius:8px;background:{{ filled($b['label']) ? $warnaModal['bg'] : '#f8fafc' }};">
                @else
                <div style="width:100%;height:140px;background:{{ filled($b['label']) ? $warnaModal['border'] : '#f1f5f9' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-file-text" style="font-size:36px;color:{{ filled($b['label']) ? $warnaModal['icon'] : '#94a3b8' }};"></i>
                </div>
                @endif
                <p style="font-size:11px;color:#94a3b8;margin:8px 0 0;">{{ $b['nama_asli'] }} &middot; buka file asli</p>
            </a>

            <form action="{{ route('siswa.arsip.berkas-lain.label', $siswa) }}" method="POST" style="display:flex;gap:6px;margin-bottom:12px;">
                @csrf
                <input type="hidden" name="index" value="{{ $b['index'] }}">
                <input type="text" name="label" value="{{ $b['label'] }}" placeholder="Jenis dokumen, mis. Sertifikat" class="form-input" style="flex:1;font-size:13px;">
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>

            <form action="{{ route('siswa.arsip.berkas-lain.pindah', $siswa) }}" method="POST" onsubmit="return confirm('Pindahkan berkas ini ke kategori terpilih?')">
                @csrf
                <input type="hidden" name="index" value="{{ $b['index'] }}">
                <label class="form-label">Atau pindahkan ke kategori</label>
                <select name="field_tujuan" onchange="this.form.submit()" class="form-input">
                    <option value="" disabled selected>-- Pindahkan ke... --</option>
                    <option value="kartu_keluarga">Kartu Keluarga</option>
                    <option value="akta_lahir">Akta Lahir</option>
                    <option value="ijazah_sd">Ijazah SD/MI</option>
                    <option value="ijazah">Ijazah SMP</option>
                    <option value="transkrip_nilai">Transkrip Nilai</option>
                    <option value="sertifikat_tka">Sertifikat TKA</option>
                </select>
            </form>
        </div>
    </div>
    @endforeach
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
