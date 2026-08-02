<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { font-size: 10px; color: #1a1a1a; margin: 0; }
    h1 { text-align:center; font-size: 13px; margin: 0 0 2px; text-transform: uppercase; }
    h2 { text-align:center; font-size: 10px; margin: 0 0 14px; font-weight: normal; }
    .identitas { display: table; width: 100%; margin-bottom: 14px; font-size: 10px; }
    .identitas-col { display: table-cell; width: 50%; }
    .identitas-row { display: table; width: 100%; margin-bottom: 2px; }
    .identitas-label { display: table-cell; width: 90px; }
    .identitas-sep { display: table-cell; width: 12px; }
    .identitas-val { display: table-cell; font-weight: bold; }

    table.nilai { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.nilai th { background: #dbeafe; border: 1px solid #333; padding: 5px; font-size: 9px; text-align: center; }
    table.nilai td { border: 1px solid #333; padding: 5px; font-size: 9px; text-align: center; }
    table.nilai td.nama { text-align: left; }

    .grid-2 { display: table; width: 100%; margin-bottom: 14px; }
    .grid-2 .col { display: table-cell; width: 48%; vertical-align: top; }
    .grid-2 .gap { display: table-cell; width: 4%; }
    .box-title { font-size: 9.5px; font-weight: bold; margin: 0 0 6px; }
    .box { border: 1px solid #333; padding: 8px; font-size: 9px; min-height: 70px; }
    .qr-box { text-align: center; }
    .qr-box img { width: 90px; height: 90px; }

    .ttd-grid { display: table; width: 100%; margin-top: 10px; }
    .ttd-col { display: table-cell; width: 33.3%; text-align: center; font-size: 9px; vertical-align: top; }
    .ttd-space { height: 45px; }
    .ttd-nama { font-weight: bold; text-decoration: underline; }

    .bar-atas { background: #1a1a1a; height: 5px; margin-bottom: 10px; }
    .kop { display: table; width: 100%; margin-bottom: 6px; }
    .kop-row { display: table-row; }
    .kop-cell { display: table-cell; vertical-align: middle; }
    .kop-logo { width: 60px; text-align: center; }
    .kop-logo img { max-width: 52px; max-height: 52px; }
    .kop-text { text-align: center; }
    .kop-text h1 { font-size: 12px; margin: 0; font-weight: bold; text-transform: none; }
    .kop-text h2 { font-size: 10px; margin: 1px 0; font-weight: normal; }
    .kop-text h3 { font-size: 13px; margin: 2px 0; font-weight: bold; color: #1d4ed8; }
    .kop-text p { font-size: 8px; margin: 1px 0; }
    .garis-tebal { border-bottom: 3px solid #000; border-top: 1px solid #000; height: 4px; margin-bottom: 10px; }
    .watermark { position: fixed; top: 30%; left: 20%; width: 60%; opacity: 0.07; z-index: -1; }
</style>
</head>
<body>

@php
    $sekolahNama = strtoupper($sekolah->nama ?? '-');
    $kabupatenText = $sekolah->kabupaten_kota ? 'PEMERINTAH ' . strtoupper($sekolah->kabupaten_kota) : '';
    $alamatBaris = trim($sekolah->alamat ?? '');
    if (!empty($sekolah->kecamatan)) $alamatBaris .= ', ' . $sekolah->kecamatan;
    $kontakBaris = [];
    if (!empty($sekolah->telepon)) $kontakBaris[] = 'Telepon ' . $sekolah->telepon;
    if (!empty($sekolah->email)) $kontakBaris[] = 'Pos-el: ' . $sekolah->email;
    $kontakBaris = implode(' &middot; ', $kontakBaris);
@endphp

@if($sekolah->rapor_tampilkan_watermark && $sekolah->watermark_rapor)
<img src="{{ public_path('storage/' . $sekolah->watermark_rapor) }}" class="watermark">
@endif

<div class="bar-atas"></div>
<div class="kop">
    <div class="kop-row">
        <div class="kop-cell kop-logo">
            @if($sekolah->rapor_tampilkan_logo && !empty($sekolah->logo_kabupaten))<img src="{{ public_path('storage/' . $sekolah->logo_kabupaten) }}" alt="">@endif
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
            @if($sekolah->rapor_tampilkan_logo && !empty($sekolah->logo_sekolah))<img src="{{ public_path('storage/' . $sekolah->logo_sekolah) }}" alt="">@endif
        </div>
    </div>
</div>
<div class="garis-tebal"></div>

<h1 style="text-transform:uppercase;margin-top:6px;">Laporan Hasil Belajar Tengah Semester</h1>
<h2>Tahun Pelajaran {{ $tahunAktif->nama }}</h2>

<div class="identitas">
    <div class="identitas-col">
        <div class="identitas-row"><div class="identitas-label">Nama Siswa</div><div class="identitas-sep">:</div><div class="identitas-val">{{ strtoupper($siswa->nama_lengkap) }}</div></div>
        <div class="identitas-row"><div class="identitas-label">NIS / NISN</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $siswa->nis }} / {{ $siswa->nisn }}</div></div>
    </div>
    <div class="identitas-col">
        <div class="identitas-row"><div class="identitas-label">Kelas</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $siswa->rombel_lengkap }}</div></div>
        <div class="identitas-row"><div class="identitas-label">Semester</div><div class="identitas-sep">:</div><div class="identitas-val">{{ $semester == 2 ? 'Genap' : 'Ganjil' }}</div></div>
    </div>
</div>

<table class="nilai">
    <thead>
        <tr>
            <th rowspan="2" style="width:24px;">No</th>
            <th rowspan="2" style="width:150px;">Mata Pelajaran</th>
            <th colspan="4">Tujuan Pembelajaran</th>
            <th rowspan="2" style="width:40px;">STS</th>
        </tr>
        <tr>
            <th>TP 1</th><th>TP 2</th><th>TP 3</th><th>TP 4</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="nama">{{ $r['mapel']->nama }}</td>
            @for($k = 0; $k < 4; $k++)
            <td>{{ $r['per_tp'][$k] ?? '-' }}</td>
            @endfor
            <td style="font-weight:bold;">{{ $r['sts'] ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7">Belum ada data penilaian.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="grid-2">
    <div class="col">
        <p class="box-title">Scan Riwayat Siswa</p>
        <div class="box qr-box">
            @if($qrPng)<img src="{{ $qrPng }}" alt="QR">@else<p style="color:#94a3b8;">QR tidak tersedia</p>@endif
        </div>
    </div>
    <div class="gap"></div>
    <div class="col">
        <p class="box-title">Catatan Wali Kelas</p>
        <div class="box">{{ $catatanWaliKelas ?: '-' }}</div>
    </div>
</div>

<div class="ttd-grid">
    <div class="ttd-col">
        Mengetahui,<br>Kepala Sekolah
        <div class="ttd-space"></div>
        <div class="ttd-nama">{{ $sekolah->kepala_sekolah_nama ?? '-' }}</div>
        <div>NIP {{ $sekolah->kepala_sekolah_nip }}</div>
    </div>
    <div class="ttd-col">
        Orang Tua
        <div class="ttd-space"></div>
        <div>( ................................. )</div>
    </div>
    <div class="ttd-col">
        {{ $sekolah->kecamatan ?? '' }}, {{ now()->translatedFormat('d F Y') }}<br>Wali Kelas
        <div class="ttd-space"></div>
        <div class="ttd-nama">&nbsp;</div>
    </div>
</div>

</body>
</html>
