@extends('layouts.app')
@section('title', 'Hasil Scan - ' . $siswa->nama_lengkap)
@section('page-title', 'Hasil Scan KK & Akta')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'ocr'])

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

@if(! $hasil->exists)
<div style="background:#eff6ff;color:#1e40af;padding:14px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;justify-content:space-between;">
    <span><i class="ti ti-info-circle"></i> Siswa ini belum pernah discan.</span>
    <div style="display:flex;gap:8px;">
        @if($siswa->arsipBerkas?->kartu_keluarga)
        <button type="button" onclick="document.getElementById('modal-lihat-kk').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-file-text"></i> Lihat KK</button>
        @endif
        <form action="{{ route('siswa.scan-kk.satu', $siswa) }}" method="POST" onsubmit="return confirm('Scan berkas KK/Akta siswa ini sekarang?')">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-scan"></i> Scan Sekarang</button>
        </form>
    </div>
</div>
@else
<div style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:16px;">
    @if($siswa->arsipBerkas?->kartu_keluarga)
    <button type="button" onclick="document.getElementById('modal-lihat-kk').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-file-text"></i> Lihat KK</button>
    @endif
    <form action="{{ route('siswa.scan-kk.satu', $siswa) }}" method="POST" onsubmit="return confirm('Scan ulang berkas KK/Akta siswa ini?')">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Scan Ulang</button>
    </form>
</div>
@endif

@if($siswa->arsipBerkas?->kartu_keluarga)
<div id="modal-lihat-kk" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:70;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:720px;width:100%;height:85vh;padding:16px;display:flex;flex-direction:column;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-id"></i> Kartu Keluarga (Lihat Saja)</p>
            <button type="button" onclick="document.getElementById('modal-lihat-kk').style.display='none'" style="border:none;background:none;font-size:22px;color:#94a3b8;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <iframe src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($siswa->arsipBerkas->kartu_keluarga) }}" style="flex:1;width:100%;border:1px solid #e2e8f0;border-radius:8px;"></iframe>
    </div>
</div>
@endif

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:-6px 0 4px;">{{ $siswa->nama_lengkap }} &middot; {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
@if($hasil->exists && $hasil->sudahDikonfirmasi())
<p style="font-size:12px;color:#16a34a;margin:0 0 16px;"><i class="ti ti-shield-check"></i> Dikonfirmasi {{ $hasil->dikonfirmasi_at->locale('id')->diffForHumans() }}</p>
@elseif($hasil->exists)
<p style="font-size:12px;color:#d97706;margin:0 0 16px;"><i class="ti ti-alert-triangle"></i> Sudah discan, tapi belum dikonfirmasi admin.</p>
@else
<div style="margin-bottom:16px;"></div>
@endif

{{-- Kartu Keluarga --}}
<div class="card" style="padding:0;overflow:hidden;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-id" style="color:#2563eb;"></i> Kartu Keluarga</p>
        @if($hasil->status_kk === 'ok')
        <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Skor: {{ $hasil->skor_kk }}%</span>
        @elseif($hasil->status_kk === 'error')
        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Error</span>
        @else
        <span style="color:#94a3b8;font-size:11px;">Belum discan</span>
        @endif
    </div>

    @if($hasil->status_kk === 'ok')
    @php
    $detailAyah = $hasil->detailAyahHasilScan();
    $detailIbu = $hasil->detailIbuHasilScan();
    @endphp
    @if(! empty($hasil->data_kk['peringatan']))
    <div style="background:#fef9c3;color:#854d0e;padding:10px 18px;font-size:12px;border-bottom:1px solid #f1f5f9;">
        <i class="ti ti-alert-triangle"></i> <strong>Perlu dicek manual:</strong> {{ $hasil->data_kk['peringatan'] }}
    </div>
    @endif
    <form action="{{ route('siswa.scan-kk.terapkan-massal', $siswa) }}" method="POST" id="form-terapkan-massal">
        @csrf
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:9px 12px;text-align:left;width:36px;"><input type="checkbox" id="cb-semua" onclick="document.querySelectorAll('.cb-terapkan').forEach(cb => cb.checked = this.checked)"></th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;width:150px;">Field</th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;">Data Induk</th>
                    <th style="padding:9px 12px;text-align:left;font-size:11px;color:#7c3aed;">Hasil Scan</th>
                </tr>
            </thead>
            <tbody>
                @php $detailSiswa = $hasil->detailSiswaHasilScan(); @endphp
                <tr><td colspan="4" style="padding:8px 12px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;background:#fafafa;">Data Siswa</td></tr>
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Nama Siswa', 'nilaiInduk' => $siswa->nama_lengkap, 'nilaiScan' => $detailSiswa['nama_lengkap'] ?? null, 'fieldTujuan' => 'nama_lengkap', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'NIK Siswa', 'nilaiInduk' => $siswa->nik, 'nilaiScan' => $detailSiswa['nik'] ?? null, 'fieldTujuan' => 'nik', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tempat Lahir', 'nilaiInduk' => $siswa->tempat_lahir, 'nilaiScan' => $detailSiswa['tempat_lahir'] ?? null, 'fieldTujuan' => 'tempat_lahir', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tanggal Lahir', 'nilaiInduk' => $siswa->tanggal_lahir?->format('d-m-Y'), 'nilaiScan' => $detailSiswa['tanggal_lahir'] ?? null, 'fieldTujuan' => 'tanggal_lahir', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'No. Kartu Keluarga', 'nilaiInduk' => $siswa->no_kk, 'nilaiScan' => $hasil->data_kk['no_kk'] ?? null, 'fieldTujuan' => 'no_kk', 'siswa' => $siswa])

                <tr><td colspan="4" style="padding:8px 12px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;background:#fafafa;">Data Keluarga</td></tr>
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Alamat', 'nilaiInduk' => $siswa->alamat, 'nilaiScan' => $hasil->data_kk['alamat'] ?? null, 'fieldTujuan' => 'alamat', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Anak ke-', 'nilaiInduk' => $siswa->anak_ke, 'nilaiScan' => $hasil->data_kk['anak_ke'] ?? null, 'fieldTujuan' => 'anak_ke', 'siswa' => $siswa])

                <tr><td colspan="4" style="padding:8px 12px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;background:#fafafa;">Data Ayah</td></tr>
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Nama Ayah', 'nilaiInduk' => $siswa->nama_ayah, 'nilaiScan' => $hasil->namaAyahHasilScan(), 'fieldTujuan' => 'nama_ayah', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'NIK Ayah', 'nilaiInduk' => $siswa->nik_ayah, 'nilaiScan' => $detailAyah['nik'] ?? null, 'fieldTujuan' => 'nik_ayah', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tahun Lahir Ayah', 'nilaiInduk' => $siswa->tahun_lahir_ayah, 'nilaiScan' => \App\Models\ScanKkHasil::ekstrakTahun($detailAyah['tanggal_lahir'] ?? null), 'fieldTujuan' => 'tahun_lahir_ayah', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Pekerjaan Ayah', 'nilaiInduk' => $siswa->pekerjaan_ayah, 'nilaiScan' => $detailAyah['jenis_pekerjaan'] ?? null, 'fieldTujuan' => 'pekerjaan_ayah', 'siswa' => $siswa])

                <tr><td colspan="4" style="padding:8px 12px;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;background:#fafafa;">Data Ibu</td></tr>
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Nama Ibu', 'nilaiInduk' => $siswa->nama_ibu, 'nilaiScan' => $hasil->namaIbuHasilScan(), 'fieldTujuan' => 'nama_ibu', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'NIK Ibu', 'nilaiInduk' => $siswa->nik_ibu, 'nilaiScan' => $detailIbu['nik'] ?? null, 'fieldTujuan' => 'nik_ibu', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Tahun Lahir Ibu', 'nilaiInduk' => $siswa->tahun_lahir_ibu, 'nilaiScan' => \App\Models\ScanKkHasil::ekstrakTahun($detailIbu['tanggal_lahir'] ?? null), 'fieldTujuan' => 'tahun_lahir_ibu', 'siswa' => $siswa])
                @include('siswa.scan-kk._baris-bandingkan', ['label' => 'Pekerjaan Ibu', 'nilaiInduk' => $siswa->pekerjaan_ibu, 'nilaiScan' => $detailIbu['jenis_pekerjaan'] ?? null, 'fieldTujuan' => 'pekerjaan_ibu', 'siswa' => $siswa])
            </tbody>
        </table>
        <div style="padding:14px 18px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <label style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" onclick="document.getElementById('cb-semua').click()"> Centang semua yang berbeda
            </label>
            <div style="display:flex;gap:8px;">
                <button type="submit" formaction="{{ route('siswa.scan-kk.tandai-update', $siswa) }}" class="btn btn-secondary btn-sm"><i class="ti ti-shield-check"></i> Update Status Saja</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-check"></i> Terapkan Perubahan</button>
            </div>
        </div>
    </form>

    <div style="padding:0 18px 14px;">
        <details>
            <summary style="font-size:11px;color:#94a3b8;cursor:pointer;">Lihat data lengkap hasil OCR</summary>
            <pre style="font-size:10px;background:#f8fafc;padding:10px;border-radius:6px;overflow-x:auto;margin-top:6px;">{{ json_encode($hasil->data_kk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </details>
    </div>
    @elseif($hasil->status_kk === 'error')
    <p style="font-size:12px;color:#991b1b;padding:14px 18px;">{{ $hasil->pesan_error }}</p>
    @else
    <p style="font-size:12px;color:#94a3b8;padding:14px 18px;">Belum ada berkas KK atau belum discan.</p>
    @endif
</div>

{{-- Akta Lahir - taruh di bawah, terpisah, biar gak ganggu tampilan tabel KK. View-only (tanpa Terapkan), krn KK & Akta jarang beda tapi tetap perlu dicek manual kalau memang beda --}}
<div class="card" style="padding:0;overflow:hidden;">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-certificate" style="color:#16a34a;"></i> Akta Lahir <span style="font-size:11px;color:#94a3b8;font-weight:400;">(cuma tampilan, bandingkan manual)</span></p>
        @if($hasil->status_akta === 'ok')
        <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Skor: {{ $hasil->skor_akta }}%</span>
        @elseif($hasil->status_akta === 'error')
        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Error</span>
        @else
        <span style="color:#94a3b8;font-size:11px;">Belum discan</span>
        @endif
    </div>

    @if($hasil->status_akta === 'ok')
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;width:160px;">Field</th>
                <th style="padding:9px 12px;text-align:left;font-size:11px;color:#94a3b8;">Data Induk</th>
                <th style="padding:9px 12px;text-align:left;font-size:11px;color:#16a34a;">Hasil Scan Akta</th>
            </tr>
        </thead>
        <tbody>
            @include('siswa.scan-kk._baris-view-saja', ['label' => 'Nama', 'nilaiInduk' => $siswa->nama_lengkap, 'nilaiScan' => $hasil->data_akta['nama_anak'] ?? null])
            @include('siswa.scan-kk._baris-view-saja', ['label' => 'Tempat Lahir', 'nilaiInduk' => $siswa->tempat_lahir, 'nilaiScan' => $hasil->data_akta['tempat_lahir'] ?? null])
            @php
            $tglIndukNormal = $siswa->tanggal_lahir?->format('d-m-Y');
            $tglAktaAsli = $hasil->data_akta['tanggal_lahir'] ?? null;
            $tglAktaNormal = \App\Models\ScanKkHasil::normalisasiTanggal($tglAktaAsli);
            $tglBerbeda = $tglAktaAsli && $tglAktaNormal !== $tglIndukNormal;
            @endphp
            @include('siswa.scan-kk._baris-view-saja', ['label' => 'Tanggal Lahir', 'nilaiInduk' => $tglIndukNormal, 'nilaiScan' => $tglAktaAsli, 'berbedaOverride' => $tglBerbeda])
        </tbody>
    </table>
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">
        <p style="font-size:11px;color:#94a3b8;margin:0;">No. Registrasi Akta (hasil scan)</p>
        <p style="font-size:15px;font-weight:700;color:#0f172a;margin:2px 0 0;font-family:monospace;">{{ $hasil->no_akta }}</p>
    </div>
    @elseif($hasil->status_akta === 'error')
    <p style="font-size:12px;color:#991b1b;padding:14px 18px;">{{ $hasil->pesan_error }}</p>
    @else
    <p style="font-size:12px;color:#94a3b8;padding:14px 18px;">Belum ada berkas Akta atau belum discan.</p>
    @endif
</div>

@endsection
