@extends('layouts.erapor')
@section('title', 'Pengaturan Cetak Rapor')
@section('page-title', 'Pengaturan Cetak Rapor')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Berlaku untuk semua rapor yang dicetak dari sekolah ini. Data kop surat (nama sekolah, alamat,
    kepala sekolah, dll) diatur di <a href="/profil-sekolah" style="color:#2563EB;">Profil Sekolah</a>, terpisah dari halaman ini.
</p>

<form action="{{ route('erapor.pengaturan-cetak.update') }}" method="POST" class="card" style="max-width:640px;padding:24px;" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    {{-- hidden supaya validasi required ambang batas tetap terpenuhi walau form ini yg disubmit --}}
    <input type="hidden" name="rapor_threshold_sangat_baik" value="{{ $sekolah->rapor_threshold_sangat_baik }}">
    <input type="hidden" name="rapor_threshold_baik" value="{{ $sekolah->rapor_threshold_baik }}">
    <input type="hidden" name="rapor_threshold_cukup" value="{{ $sekolah->rapor_threshold_cukup }}">

    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Kertas & Tata Letak</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
        <div>
            <label class="form-label">Ukuran Kertas</label>
            <select name="rapor_ukuran_kertas" class="form-input">
                @foreach(['A4','F4','Legal'] as $u)
                <option value="{{ $u }}" {{ $sekolah->rapor_ukuran_kertas === $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Orientasi</label>
            <select name="rapor_orientasi" class="form-input">
                <option value="portrait" {{ $sekolah->rapor_orientasi === 'portrait' ? 'selected' : '' }}>Potret (Portrait)</option>
                <option value="landscape" {{ $sekolah->rapor_orientasi === 'landscape' ? 'selected' : '' }}>Lanskap (Landscape)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Ukuran Font</label>
            <select name="rapor_font_size" class="form-input">
                <option value="kecil" {{ $sekolah->rapor_font_size === 'kecil' ? 'selected' : '' }}>Kecil (muat lebih banyak teks)</option>
                <option value="normal" {{ $sekolah->rapor_font_size === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="besar" {{ $sekolah->rapor_font_size === 'besar' ? 'selected' : '' }}>Besar (lebih mudah dibaca)</option>
            </select>
        </div>
        <div style="display:flex;align-items:center;gap:8px;padding-top:26px;">
            <input type="checkbox" name="rapor_tampilkan_logo" value="1" id="tampilkan_logo" {{ $sekolah->rapor_tampilkan_logo ? 'checked' : '' }}>
            <label for="tampilkan_logo" style="font-size:13px;">Tampilkan logo di kop surat</label>
        </div>
    </div>

    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Logo & Watermark</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
        <div>
            <label class="form-label">Logo Kabupaten/Kota (kiri)</label>
            @if($sekolah->logo_kabupaten)
            <div style="margin-bottom:6px;"><img src="{{ asset('storage/' . $sekolah->logo_kabupaten) }}" style="height:50px;"></div>
            @endif
            <input type="file" name="logo_kabupaten" accept="image/*" class="form-input">
        </div>
        <div>
            <label class="form-label">Logo Sekolah (kanan)</label>
            @if($sekolah->logo_sekolah)
            <div style="margin-bottom:6px;"><img src="{{ asset('storage/' . $sekolah->logo_sekolah) }}" style="height:50px;"></div>
            @endif
            <input type="file" name="logo_sekolah" accept="image/*" class="form-input">
        </div>
    </div>
    <div style="margin-bottom:18px;">
        <label class="form-label">Watermark Background Rapor</label>
        @if($sekolah->watermark_rapor)
        <div style="margin-bottom:6px;"><img src="{{ asset('storage/' . $sekolah->watermark_rapor) }}" style="height:60px;opacity:.5;"></div>
        @endif
        <input type="file" name="watermark_rapor" accept="image/*" class="form-input" style="margin-bottom:8px;">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="rapor_tampilkan_watermark" value="1" {{ $sekolah->rapor_tampilkan_watermark ? 'checked' : '' }}>
            <span style="font-size:13px;">Tampilkan watermark ini di background rapor</span>
        </label>
    </div>

    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:18px;">
        <p style="font-size:13px;font-weight:700;color:#1e40af;margin:0 0 4px;">Opsi Lain: Upload Header Lengkap</p>
        <p style="font-size:12px;color:#1e40af;margin:0 0 10px;">
            Kalau susunan logo+teks otomatis di atas kurang sama persis dengan kop surat resmi sekolahmu, upload
            1 gambar header utuh (logo+teks jadi satu) di sini - ini akan dipakai apa adanya, menggantikan susunan otomatis.
        </p>
        @if($sekolah->rapor_header_custom)
        <div style="margin-bottom:8px;background:#fff;padding:8px;border-radius:8px;"><img src="{{ asset('storage/' . $sekolah->rapor_header_custom) }}" style="max-width:100%;"></div>
        @endif
        <input type="file" name="rapor_header_custom" accept="image/*" class="form-input" style="margin-bottom:8px;">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="rapor_pakai_header_custom" value="1" {{ $sekolah->rapor_pakai_header_custom ? 'checked' : '' }}>
            <span style="font-size:13px;color:#1e40af;">Pakai header custom ini (bukan susunan logo+teks otomatis di atas)</span>
        </label>
    </div>

    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Tanggal & Tanda Tangan</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:8px;">
        <div>
            <label class="form-label">Tanggal Cetak (opsional)</label>
            <input type="date" name="rapor_tanggal_manual" value="{{ $sekolah->rapor_tanggal_manual?->format('Y-m-d') }}" class="form-input">
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Kosongkan supaya otomatis pakai tanggal hari ini saat dicetak. Isi kalau mau tanggal seragam (mis. tanggal pembagian rapor).</p>
        </div>
        <div>
            <label class="form-label">Kota (baris tanda tangan)</label>
            <input type="text" name="rapor_kota_ttd" value="{{ $sekolah->rapor_kota_ttd }}" class="form-input" placeholder="mis. Turen">
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Kosongkan supaya otomatis pakai nama kecamatan dari Profil Sekolah.</p>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:12px;"><i class="ti ti-device-floppy"></i> Simpan Pengaturan</button>
</form>

<form action="{{ route('erapor.pengaturan-cetak.update') }}" method="POST" class="card" style="max-width:640px;padding:24px;margin-top:16px;">
    @csrf
    @method('PUT')
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Ambang Batas Deskripsi Capaian Kompetensi</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 14px;">
        Menentukan kata "sangat baik/baik/cukup/perlu penguatan" di deskripsi rapor berdasarkan rata-rata
        skor TP. Default persis sesuai sistem lama (93/84/75) - berlaku otomatis, ubah kalau perlu.
    </p>

    {{-- field tersembunyi supaya form ini submit ke route yg sama tanpa duplikasi input lain --}}
    <input type="hidden" name="rapor_ukuran_kertas" value="{{ $sekolah->rapor_ukuran_kertas }}">
    <input type="hidden" name="rapor_orientasi" value="{{ $sekolah->rapor_orientasi }}">
    <input type="hidden" name="rapor_font_size" value="{{ $sekolah->rapor_font_size }}">
    <input type="hidden" name="rapor_tanggal_manual" value="{{ $sekolah->rapor_tanggal_manual?->format('Y-m-d') }}">
    <input type="hidden" name="rapor_tampilkan_logo" value="{{ $sekolah->rapor_tampilkan_logo ? '1' : '0' }}">
    <input type="hidden" name="rapor_tampilkan_watermark" value="{{ $sekolah->rapor_tampilkan_watermark ? '1' : '0' }}">
    <input type="hidden" name="rapor_pakai_header_custom" value="{{ $sekolah->rapor_pakai_header_custom ? '1' : '0' }}">
    <input type="hidden" name="rapor_kota_ttd" value="{{ $sekolah->rapor_kota_ttd }}">

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
        <div>
            <label class="form-label">Sangat Baik (≥)</label>
            <input type="number" name="rapor_threshold_sangat_baik" value="{{ $sekolah->rapor_threshold_sangat_baik }}" min="0" max="100" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Baik (≥)</label>
            <input type="number" name="rapor_threshold_baik" value="{{ $sekolah->rapor_threshold_baik }}" min="0" max="100" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Cukup (≥)</label>
            <input type="number" name="rapor_threshold_cukup" value="{{ $sekolah->rapor_threshold_cukup }}" min="0" max="100" class="form-input" required>
        </div>
    </div>
    <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Di bawah angka "Cukup" otomatis jadi "Perlu penguatan dalam ...".</p>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:16px;"><i class="ti ti-device-floppy"></i> Simpan Ambang Batas</button>
</form>
@endsection
