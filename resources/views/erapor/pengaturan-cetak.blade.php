@extends('layouts.erapor')
@section('title', 'Pengaturan Cetak')
@section('page-title', 'Pengaturan Cetak')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Berlaku untuk semua rapor & UTS yang dicetak dari sekolah ini. Data kop surat (nama sekolah, alamat,
    kepala sekolah, dll) diatur di <a href="/profil-sekolah" style="color:#2563EB;">Profil Sekolah</a>, terpisah dari halaman ini.
</p>

<form action="{{ route('erapor.pengaturan-cetak.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- ══════════════ 1. PENGATURAN UMUM (dipakai bareng Rapor & UTS) ══════════════ --}}
    <div class="card" style="max-width:680px;padding:24px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <span class="badge" style="background:#1E3A5F;color:#fff;">1</span>
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Pengaturan Umum</p>
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 18px;">Kop surat, logo, watermark - sama persis dipakai di Rapor semester maupun UTS/PTS.</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
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
        <label style="display:flex;align-items:center;gap:8px;margin-bottom:18px;">
            <input type="checkbox" name="rapor_tampilkan_logo" value="1" {{ $sekolah->rapor_tampilkan_logo ? 'checked' : '' }}>
            <span style="font-size:13px;">Tampilkan logo di kop surat</span>
        </label>

        <div style="margin-bottom:18px;">
            <label class="form-label">Watermark Background</label>
            @if($sekolah->watermark_rapor)
            <div style="margin-bottom:6px;"><img src="{{ asset('storage/' . $sekolah->watermark_rapor) }}" style="height:60px;opacity:.5;"></div>
            @endif
            <input type="file" name="watermark_rapor" accept="image/*" class="form-input" style="margin-bottom:8px;">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="rapor_tampilkan_watermark" value="1" {{ $sekolah->rapor_tampilkan_watermark ? 'checked' : '' }}>
                <span style="font-size:13px;">Tampilkan watermark ini di background</span>
            </label>
        </div>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;margin-bottom:18px;">
            <p style="font-size:13px;font-weight:700;color:#1e40af;margin:0 0 4px;">Opsi Lain: Upload Header Lengkap</p>
            <p style="font-size:12px;color:#1e40af;margin:0 0 10px;">
                Kalau susunan logo+teks otomatis kurang sama persis dgn kop surat resmi sekolahmu, upload 1 gambar
                header utuh (logo+teks jadi satu) - dipakai apa adanya, menggantikan susunan otomatis.
            </p>
            @if($sekolah->rapor_header_custom)
            <div style="margin-bottom:8px;background:#fff;padding:8px;border-radius:8px;"><img src="{{ asset('storage/' . $sekolah->rapor_header_custom) }}" style="max-width:100%;"></div>
            @endif
            <input type="file" name="rapor_header_custom" accept="image/*" class="form-input" style="margin-bottom:8px;">
            <div style="margin-bottom:8px;">
                <label class="form-label" style="color:#1e40af;">Ukuran Header (persentase)</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="range" name="rapor_header_custom_scale" min="30" max="150" value="{{ $sekolah->rapor_header_custom_scale ?? 100 }}"
                           oninput="document.getElementById('scale-preview').textContent = this.value + '%'" style="flex:1;">
                    <span id="scale-preview" style="font-size:13px;font-weight:700;color:#1e40af;min-width:45px;">{{ $sekolah->rapor_header_custom_scale ?? 100 }}%</span>
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="rapor_pakai_header_custom" value="1" {{ $sekolah->rapor_pakai_header_custom ? 'checked' : '' }}>
                <span style="font-size:13px;color:#1e40af;">Pakai header custom ini</span>
            </label>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label class="form-label">Tanggal Cetak (opsional)</label>
                <input type="date" name="rapor_tanggal_manual" value="{{ $sekolah->rapor_tanggal_manual?->format('Y-m-d') }}" class="form-input">
                <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Kosongkan supaya otomatis pakai tanggal hari ini.</p>
            </div>
            <div>
                <label class="form-label">Kota (baris tanda tangan)</label>
                <input type="text" name="rapor_kota_ttd" value="{{ $sekolah->rapor_kota_ttd }}" class="form-input" placeholder="mis. Turen">
                <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Kosongkan supaya otomatis pakai nama kecamatan.</p>
            </div>
        </div>
    </div>

    {{-- ══════════════ 2. PENGATURAN RAPOR (semester) ══════════════ --}}
    <div class="card" style="max-width:680px;padding:24px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <span class="badge" style="background:#2563EB;color:#fff;">2</span>
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Pengaturan Rapor (Akhir Semester)</p>
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 18px;">Khusus dokumen rapor semester (bukan UTS).</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
            <div>
                <label class="form-label">Ukuran Kertas</label>
                <select name="rapor_ukuran_kertas" class="form-input">
                    @foreach(['A4','F4','Legal'] as $u)<option value="{{ $u }}" {{ $sekolah->rapor_ukuran_kertas === $u ? 'selected' : '' }}>{{ $u }}</option>@endforeach
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
        </div>

        <div style="margin-bottom:18px;">
            <label class="form-label">Warna Dasar Tabel</label>
            <div style="display:flex;gap:10px;margin-top:6px;">
                @foreach(['biru' => ['Biru Muda', '#dbeafe'], 'hijau' => ['Hijau', '#dcfce7'], 'kuning' => ['Kuning', '#fef9c3']] as $val => $info)
                <label style="flex:1;display:flex;align-items:center;gap:8px;border:2px solid {{ $sekolah->rapor_warna_tabel === $val ? '#1E3A5F' : '#e2e8f0' }};border-radius:8px;padding:10px 12px;cursor:pointer;">
                    <input type="radio" name="rapor_warna_tabel" value="{{ $val }}" {{ $sekolah->rapor_warna_tabel === $val ? 'checked' : '' }}>
                    <span style="width:20px;height:20px;border-radius:5px;background:{{ $info[1] }};display:inline-block;border:1px solid #cbd5e1;"></span>
                    <span style="font-size:13px;">{{ $info[0] }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Ambang Batas Deskripsi Capaian Kompetensi</p>
        <p style="font-size:12px;color:#64748b;margin:0 0 14px;">
            Menentukan kata "sangat baik/baik/cukup/perlu penguatan" berdasarkan rata-rata skor TP. Default 93/84/75.
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
            <div><label class="form-label">Sangat Baik (≥)</label><input type="number" name="rapor_threshold_sangat_baik" value="{{ $sekolah->rapor_threshold_sangat_baik }}" min="0" max="100" class="form-input" required></div>
            <div><label class="form-label">Baik (≥)</label><input type="number" name="rapor_threshold_baik" value="{{ $sekolah->rapor_threshold_baik }}" min="0" max="100" class="form-input" required></div>
            <div><label class="form-label">Cukup (≥)</label><input type="number" name="rapor_threshold_cukup" value="{{ $sekolah->rapor_threshold_cukup }}" min="0" max="100" class="form-input" required></div>
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Di bawah angka "Cukup" otomatis jadi "Perlu penguatan dalam ...".</p>
    </div>

    {{-- ══════════════ 3. PENGATURAN UTS/PTS ══════════════ --}}
    <div class="card" style="max-width:680px;padding:24px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <span class="badge" style="background:#0891b2;color:#fff;">3</span>
            <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Pengaturan UTS/PTS</p>
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 18px;">Khusus dokumen UTS/PTS (tabel lebih lebar - default Folio/F4).</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
            <div>
                <label class="form-label">Ukuran Kertas</label>
                <select name="uts_ukuran_kertas" class="form-input">
                    @foreach(['A4','F4','Legal'] as $u)<option value="{{ $u }}" {{ $sekolah->uts_ukuran_kertas === $u ? 'selected' : '' }}>{{ $u }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Orientasi</label>
                <select name="uts_orientasi" class="form-input">
                    <option value="portrait" {{ $sekolah->uts_orientasi === 'portrait' ? 'selected' : '' }}>Potret (Portrait)</option>
                    <option value="landscape" {{ $sekolah->uts_orientasi === 'landscape' ? 'selected' : '' }}>Lanskap (Landscape)</option>
                </select>
            </div>
            <div>
                <label class="form-label">Ukuran Font</label>
                <select name="uts_font_size" class="form-input">
                    <option value="kecil" {{ $sekolah->uts_font_size === 'kecil' ? 'selected' : '' }}>Kecil (muat lebih banyak teks)</option>
                    <option value="normal" {{ $sekolah->uts_font_size === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="besar" {{ $sekolah->uts_font_size === 'besar' ? 'selected' : '' }}>Besar (lebih mudah dibaca)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="form-label">Warna Dasar Tabel</label>
            <div style="display:flex;gap:10px;margin-top:6px;">
                @foreach(['biru' => ['Biru Muda', '#dbeafe'], 'hijau' => ['Hijau', '#dcfce7'], 'kuning' => ['Kuning', '#fef9c3']] as $val => $info)
                <label style="flex:1;display:flex;align-items:center;gap:8px;border:2px solid {{ $sekolah->uts_warna_tabel === $val ? '#1E3A5F' : '#e2e8f0' }};border-radius:8px;padding:10px 12px;cursor:pointer;">
                    <input type="radio" name="uts_warna_tabel" value="{{ $val }}" {{ $sekolah->uts_warna_tabel === $val ? 'checked' : '' }}>
                    <span style="width:20px;height:20px;border-radius:5px;background:{{ $info[1] }};display:inline-block;border:1px solid #cbd5e1;"></span>
                    <span style="font-size:13px;">{{ $info[0] }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div style="max-width:680px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:14px;"><i class="ti ti-device-floppy"></i> Simpan Semua Pengaturan</button>
    </div>
</form>
@endsection
