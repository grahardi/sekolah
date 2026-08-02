<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { font-size: {{ $sekolah->rapor_font_size === 'kecil' ? '9px' : ($sekolah->rapor_font_size === 'besar' ? '11px' : '10px') }}; color: #1a1a1a; margin: 0; }
    .kop { display: table; width: 100%; margin-bottom: 6px; }
    .kop-row { display: table-row; }
    .kop-cell { display: table-cell; vertical-align: middle; }
    .kop-logo { width: 70px; text-align: center; }
    .kop-logo img { max-width: 60px; max-height: 60px; }
    .kop-text { text-align: center; }
    .kop-text h1 { font-size: 13px; margin: 0; font-weight: bold; }
    .kop-text h2 { font-size: 11px; margin: 1px 0; }
    .kop-text h3 { font-size: 14px; margin: 2px 0; font-weight: bold; color: #1d4ed8; }
    .kop-text p { font-size: 8.5px; margin: 1px 0; }
    .garis-tebal { border-bottom: 3px solid #000; border-top: 1px solid #000; height: 4px; margin-bottom: 10px; }

    .identitas { display: table; width: 100%; margin-bottom: 14px; font-size: 10px; }
    .identitas-col { display: table-cell; width: 50%; vertical-align: top; }
    .identitas-row { display: table; width: 100%; margin-bottom: 2px; }
    .identitas-label { display: table-cell; width: 110px; }
    .identitas-sep { display: table-cell; width: 12px; }
    .identitas-val { display: table-cell; font-weight: bold; }

    .section-title { font-size: 11px; font-weight: bold; margin: 14px 0 6px; }

    table.nilai { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.nilai th { background: #dbeafe; border: 1px solid #333; padding: 5px 6px; font-size: 9.5px; text-align: center; }
    table.nilai td { border: 1px solid #333; padding: 5px 6px; font-size: 9px; vertical-align: top; }
    table.nilai td.center { text-align: center; }

    .box { border: 1px solid #333; padding: 8px 10px; font-size: 9.5px; margin-bottom: 10px; }
    .box-header { background: #dbeafe; border: 1px solid #333; padding: 5px 8px; font-size: 10.5px; font-weight: bold; margin-bottom: 0; }

    .grid-2 { display: table; width: 100%; }
    .grid-2 .col { display: table-cell; width: 48%; vertical-align: top; }
    .grid-2 .gap { display: table-cell; width: 4%; }

    table.absensi { width: 100%; border-collapse: collapse; }
    table.absensi td { border: 1px solid #333; padding: 5px 8px; font-size: 9.5px; }
    table.absensi td:last-child { width: 60px; text-align: right; }

    .ttd-grid { display: table; width: 100%; margin-top: 16px; }
    .ttd-col { display: table-cell; width: 33.3%; text-align: center; font-size: 9.5px; vertical-align: top; }
    .ttd-space { height: 55px; }
    .ttd-nama { font-weight: bold; text-decoration: underline; }

    .footer-fixed { position: fixed; bottom: -20px; left: 0; right: 0; font-size: 8px; color: #666; display: table; width: 100%; }
    .footer-fixed .f-left { display: table-cell; text-align: left; font-style: italic; }
    .footer-fixed .f-right { display: table-cell; text-align: right; font-style: italic; }

    .page-break { page-break-before: always; }
    .watermark { position: fixed; top: 30%; left: 20%; width: 60%; opacity: 0.07; z-index: -1; }
    .bar-atas { background: #1a1a1a; height: 5px; margin-bottom: 10px; }
</style>
</head>
<body>

@php
    $sekolahNama = strtoupper($sekolah->nama ?? '-');
    $kabupatenText = $sekolah->kabupaten_kota ? 'PEMERINTAH ' . strtoupper($sekolah->kabupaten_kota) : '';
    $siswa = $rapor->siswa;
    $kkm = $sekolah->kkm ?? 75;

    $alamatBaris = trim($sekolah->alamat ?? '');
    if (!empty($sekolah->kecamatan)) $alamatBaris .= ', ' . $sekolah->kecamatan;
    if (!empty($sekolah->kabupaten_kota)) $alamatBaris .= ', ' . $sekolah->kabupaten_kota;

    $kontakBaris = [];
    if (!empty($sekolah->telepon)) $kontakBaris[] = 'Telepon ' . $sekolah->telepon;
    if (!empty($sekolah->email)) $kontakBaris[] = 'Pos-el: ' . $sekolah->email;
    $kontakBaris = implode(' &middot; ', $kontakBaris);

    $kelasInt = (int) $siswa->kelas;
    if ($kelasInt <= 6) { $fase = 'C'; }
    elseif ($kelasInt <= 9) { $fase = 'D'; }
    else { $fase = 'E'; }
@endphp

@if($sekolah->rapor_tampilkan_watermark && $sekolah->watermark_rapor)
<img src="{{ public_path('storage/' . $sekolah->watermark_rapor) }}" class="watermark">
@endif

{{-- ================= HALAMAN 1 ================= --}}
<div class="bar-atas"></div>
<div class="kop">
    <div class="kop-row">
        <div class="kop-cell kop-logo">
            @if(!empty($sekolah->logo_kabupaten))<img src="{{ public_path('storage/' . $sekolah->logo_kabupaten) }}" alt="">@endif
        </div>
        <div class="kop-cell kop-text">
            @if($kabupatenText)<h1>{{ $kabupatenText }}</h1>@endif
            <h2>DINAS PENDIDIKAN</h2>
            <h3>{{ $sekolahNama }}</h3>
            <p>{{ $alamatBaris }}</p>
            @if($kontakBaris)<p>{!! $kontakBaris !!}</p>@endif
            @if(!empty($sekolah->website))<p>Laman: {{ $sekolah->website }}</p>@endif
        </div>
        <div class="kop-cell kop-logo">
            @if(!empty($sekolah->logo_sekolah))<img src="{{ public_path('storage/' . $sekolah->logo_sekolah) }}" alt="">@endif
        </div>
    </div>
</div>
<div class="garis-tebal"></div>

<div class="identitas">
    <div class="identitas-col">
        <div class="identitas-row"><div class="identitas-label">Nama Murid</div><div class="identitas-sep">:</div><div class="identitas-val">{{ strtoupper($siswa->nama_lengkap) }}</div></div>
        <div class="identitas-row"><div class="identitas-label">NIS / NISN</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $siswa->nis }} / {{ $siswa->nisn }}</div></div>
        <div class="identitas-row"><div class="identitas-label">Sekolah</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $sekolahNama }}</div></div>
        <div class="identitas-row"><div class="identitas-label">Alamat Sekolah</div><div class="identitas-sep">:</div><div class="identitas-val" style="font-weight:normal;">{{ $sekolah->alamat }}</div></div>
    </div>
    <div class="identitas-col">
        <div class="identitas-row"><div class="identitas-label">Kelas</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $rapor->kelas_lengkap }}</div></div>
        <div class="identitas-row"><div class="identitas-label">Fase</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $fase }}</div></div>
        <div class="identitas-row"><div class="identitas-label">Semester</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $rapor->semester }} ({{ $rapor->semester == 1 ? 'Ganjil' : 'Genap' }})</div></div>
        <div class="identitas-row"><div class="identitas-label">Tahun Ajaran</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $rapor->tahunAjaran->nama }}</div></div>
    </div>
</div>

<p class="section-title">A. NILAI AKADEMIK</p>
<table class="nilai">
    <thead>
        <tr><th style="width:26px;">No.</th><th style="width:140px;">Mata Pelajaran</th><th style="width:50px;">Nilai Akhir</th><th>Capaian Kompetensi</th></tr>
    </thead>
    <tbody>
        @forelse($rapor->detailAkademik as $i => $d)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $d->mataPelajaran->nama }}</td>
            <td class="center" style="font-weight:bold;">{{ $d->nilai_katrol ?? $d->nilai_akhir ?? '-' }}</td>
            <td>{{ $d->capaian_kompetensi ?: '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="center">Belum ada nilai.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer-fixed"><div class="f-left">{{ $sekolahNama }}</div><div class="f-right">{{ strtoupper($siswa->nama_lengkap) }}</div></div>

{{-- ================= HALAMAN 2 ================= --}}
<div class="page-break"></div>

<p class="box-header">B. KOKURIKULER</p>
<div class="box">{{ $rapor->deskripsi_kokurikuler ?: 'Belum ada catatan kokurikuler.' }}</div>

<p class="box-header">C. EKSTRAKURIKULER</p>
<table class="nilai" style="margin-bottom:14px;">
    <thead><tr><th style="width:26px;">No.</th><th style="width:160px;">Ekstrakurikuler</th><th>Keterangan</th></tr></thead>
    <tbody>
        @forelse($rapor->detailEkskul as $i => $e)
        <tr><td class="center">{{ $i + 1 }}</td><td>{{ $e->nama_ekskul }}</td><td>{{ $e->keterangan }}</td></tr>
        @empty
        <tr><td colspan="3" class="center">Tidak mengikuti kegiatan ekstrakurikuler.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="grid-2">
    <div class="col">
        <p class="box-header">D. KETIDAKHADIRAN</p>
        <table class="absensi">
            <tr><td>Sakit</td><td>: {{ $rapor->sakit }} hari</td></tr>
            <tr><td>Izin</td><td>: {{ $rapor->izin }} hari</td></tr>
            <tr><td>Tanpa Keterangan</td><td>: {{ $rapor->tanpa_keterangan }} hari</td></tr>
        </table>
    </div>
    <div class="gap"></div>
    <div class="col">
        <p class="box-header">E. CATATAN WALI KELAS</p>
        <div class="box" style="min-height:50px;">{{ $rapor->catatan_wali_kelas ?: '-' }}</div>
    </div>
</div>

@if($rapor->keterangan_kelulusan && $rapor->semester == 2)
<p style="font-size:10px;font-weight:bold;margin:12px 0 4px;">Keterangan Kelulusan</p>
<div class="box">
    Berdasarkan hasil penilaian semester 1 dan 2, maka ananda {{ strtoupper($siswa->nama_lengkap) }} ditetapkan <strong>{{ strtoupper($rapor->keterangan_kelulusan) }}</strong>
</div>
@endif

<p style="font-size:10px;font-weight:bold;margin:12px 0 4px;">Tanggapan Orang Tua/Wali Murid</p>
<div class="box" style="min-height:40px;">&nbsp;</div>

<div class="ttd-grid">
    <div class="ttd-col">
        Orang Tua/Wali Murid
        <div class="ttd-space"></div>
        <div>( ................................. )</div>
    </div>
    <div class="ttd-col">
        Mengetahui,<br>Kepala Sekolah
        <div class="ttd-space"></div>
        <div class="ttd-nama">{{ $sekolah->kepala_sekolah_nama ?? '-' }}</div>
        <div>{{ $sekolah->kepala_sekolah_pangkat }}</div>
        <div>NIP {{ $sekolah->kepala_sekolah_nip }}</div>
    </div>
    <div class="ttd-col">
        {{ $kotaTtd ?? '' }}, {{ $tanggalCetak->translatedFormat('d F Y') }}<br>Wali Kelas
        <div class="ttd-space"></div>
        <div class="ttd-nama">{{ $waliKelas->nama ?? '-' }}</div>
        <div>{{ $waliKelas && $waliKelas->nip_nuptk ? 'NIP ' . $waliKelas->nip_nuptk : '' }}</div>
    </div>
</div>

<div class="footer-fixed"><div class="f-left">{{ $sekolahNama }}</div><div class="f-right">{{ strtoupper($siswa->nama_lengkap) }}</div></div>

</body>
</html>
