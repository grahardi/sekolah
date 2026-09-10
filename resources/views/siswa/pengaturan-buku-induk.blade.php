@extends('layouts.app')
@section('title', 'Pengaturan Buku Induk')
@section('page-title', 'Pengaturan Buku Induk')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<form action="{{ route('siswa.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card" style="padding:20px;max-width:500px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Tanggal Cetak Biodata Rapor</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Dipakai untuk tanda tangan di cetak "Biodata Rapor" (perorangan maupun massal). Kosongkan supaya otomatis pakai tanggal hari ini.</p>
        <input type="date" name="biodata_tanggal_manual" value="{{ $sekolah->biodata_tanggal_manual?->format('Y-m-d') }}" class="form-input">
    </div>

    <div class="card" style="padding:20px;max-width:500px;margin-top:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Watermark - Buku Induk</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Watermark khusus cetak Buku Induk. Isi teks ATAU upload gambar/logo (kalau ada gambar, teks diabaikan).</p>

        <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;cursor:pointer;">
            <input type="checkbox" name="watermark_induk_aktif" value="1" {{ $sekolah->watermark_induk_aktif ? 'checked' : '' }}>
            <span style="font-size:13px;color:#374151;">Aktifkan watermark Buku Induk</span>
        </label>

        <label class="form-label">Teks Watermark</label>
        <input type="text" name="watermark_induk_teks" value="{{ $sekolah->watermark_induk_teks }}" class="form-input" placeholder="mis. nama sekolah" style="margin-bottom:14px;">

        <label class="form-label">Atau Upload Gambar/Logo</label>
        @if($sekolah->watermark_induk_gambar)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->watermark_induk_gambar) }}" style="width:50px;height:50px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;">
            <button type="button" onclick="hapusWatermark('induk')" class="btn btn-secondary btn-sm" style="color:#dc2626;">Hapus Gambar</button>
        </div>
        @endif
        <input type="file" name="watermark_induk_gambar_file" accept=".jpg,.jpeg,.png" class="form-input" style="margin-bottom:14px;">

        <label class="form-label">Transparansi (1 = paling samar, 100 = paling pekat)</label>
        <input type="number" name="watermark_induk_transparansi" value="{{ $sekolah->watermark_induk_transparansi ?? 10 }}" min="1" max="100" class="form-input" style="margin-bottom:16px;">

        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Warna Kotak Judul Seksi</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Warna latar & teks kotak judul seperti "DATA DIRI", "DATA KEPENDIDIKAN", dll di cetak Buku Induk.</p>
        <div style="display:flex;gap:16px;">
            <div>
                <label class="form-label">Warna Latar (Fill)</label>
                <input type="color" name="box_fill_induk" value="{{ $sekolah->box_fill_induk ?? '#000000' }}" style="width:60px;height:36px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;">
            </div>
            <div>
                <label class="form-label">Warna Tulisan (Font)</label>
                <input type="color" name="box_font_induk" value="{{ $sekolah->box_font_induk ?? '#ffffff' }}" style="width:60px;height:36px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;">
            </div>
            <div>
                <label class="form-label">Ukuran Font (pt)</label>
                <input type="number" name="box_font_size_induk" value="{{ $sekolah->box_font_size_induk ?? 9 }}" min="5" max="20" class="form-input" style="width:70px;">
            </div>
        </div>
    </div>

    <div class="card" style="padding:20px;max-width:500px;margin-top:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Watermark - Biodata Rapor</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Watermark khusus cetak Biodata Rapor, terpisah dari Buku Induk - aktifkan salah satu saja kalau perlu.</p>

        <label style="display:flex;align-items:center;gap:8px;margin-bottom:14px;cursor:pointer;">
            <input type="checkbox" name="watermark_biodata_aktif" value="1" {{ $sekolah->watermark_biodata_aktif ? 'checked' : '' }}>
            <span style="font-size:13px;color:#374151;">Aktifkan watermark Biodata Rapor</span>
        </label>

        <label class="form-label">Teks Watermark</label>
        <input type="text" name="watermark_biodata_teks" value="{{ $sekolah->watermark_biodata_teks }}" class="form-input" placeholder="mis. nama sekolah" style="margin-bottom:14px;">

        <label class="form-label">Atau Upload Gambar/Logo</label>
        @if($sekolah->watermark_biodata_gambar)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($sekolah->watermark_biodata_gambar) }}" style="width:50px;height:50px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;">
            <button type="button" onclick="hapusWatermark('biodata')" class="btn btn-secondary btn-sm" style="color:#dc2626;">Hapus Gambar</button>
        </div>
        @endif
        <input type="file" name="watermark_biodata_gambar_file" accept=".jpg,.jpeg,.png" class="form-input" style="margin-bottom:14px;">

        <label class="form-label">Transparansi (1 = paling samar, 100 = paling pekat)</label>
        <input type="number" name="watermark_biodata_transparansi" value="{{ $sekolah->watermark_biodata_transparansi ?? 10 }}" min="1" max="100" class="form-input" style="margin-bottom:16px;">

        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Warna Kotak Judul Seksi</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Warna latar & teks kotak judul seperti "DATA DIRI", "DATA KEPENDIDIKAN", dll di cetak Biodata Rapor.</p>
        <div style="display:flex;gap:16px;">
            <div>
                <label class="form-label">Warna Latar (Fill)</label>
                <input type="color" name="box_fill_biodata" value="{{ $sekolah->box_fill_biodata ?? '#000000' }}" style="width:60px;height:36px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;">
            </div>
            <div>
                <label class="form-label">Warna Tulisan (Font)</label>
                <input type="color" name="box_font_biodata" value="{{ $sekolah->box_font_biodata ?? '#ffffff' }}" style="width:60px;height:36px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;">
            </div>
            <div>
                <label class="form-label">Ukuran Font (pt)</label>
                <input type="number" name="box_font_size_biodata" value="{{ $sekolah->box_font_size_biodata ?? 9 }}" min="5" max="20" class="form-input" style="width:70px;">
            </div>
        </div>
    </div>

    <div style="margin-top:16px;max-width:500px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Pengaturan</button>
    </div>
</form>

<form id="form-hapus-watermark" action="{{ route('siswa.pengaturan.hapus-watermark') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="jenis" id="input-jenis-hapus">
</form>
<script>
function hapusWatermark(jenis) {
    if (!confirm('Hapus gambar watermark ini?')) return;
    document.getElementById('input-jenis-hapus').value = jenis;
    document.getElementById('form-hapus-watermark').submit();
}
</script>

@endsection
