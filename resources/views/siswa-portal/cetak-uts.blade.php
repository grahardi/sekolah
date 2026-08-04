<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
    body { font-size: {{ ($sekolah->uts_font_size ?? 'normal') === 'kecil' ? '10px' : (($sekolah->uts_font_size ?? 'normal') === 'besar' ? '13px' : '11.5px') }}; color: #1a1a1a; margin: 0; }
    h1 { text-align:center; font-size: 15px; margin: 0 0 3px; text-transform: uppercase; }
    h2 { text-align:center; font-size: 11.5px; margin: 0 0 16px; font-weight: normal; }
    .identitas { display: table; width: 100%; margin-bottom: 16px; font-size: 11.5px; }
    .identitas-col { display: table-cell; width: 50%; }
    .identitas-row { display: table; width: 100%; margin-bottom: 3px; }
    .identitas-label { display: table-cell; width: 100px; }
    .identitas-sep { display: table-cell; width: 12px; }
    .identitas-val { display: table-cell; font-weight: bold; }

    .nilai-wrap { display: table; width: 100%; border-collapse: collapse; margin-bottom: 16px; table-layout: fixed; border: 1px solid #333; }
    .nilai-row { display: table-row; }
    .nilai-row > div { display: table-cell; border: 1px solid #333; padding: 5px 6px; font-size: 11px; text-align: center; vertical-align: middle; line-height: 1.3; }
    .nilai-head-1 > div, .nilai-head-2 > div { background: {{ ['biru'=>'#dbeafe','hijau'=>'#dcfce7','kuning'=>'#fef9c3'][$sekolah->uts_warna_tabel ?? 'biru'] }}; font-weight: bold; font-size: 10.5px; }
    .c-no { width: 4%; }
    .c-mapel { width: 40%; text-align: left; }
    .c-mapel-body { text-align: left; font-weight: normal; font-size: 11px; }
    .c-tp { width: 9%; }
    .c-sts { width: 10%; }
    .c-komponen { width: 44%; }
    .c-penilaian { width: 56%; }
    .c-nilai-angka { font-weight: bold; font-size: 13px; }
    .c-sts-angka { font-size: 14px; }

    .grid-2 { display: table; width: 100%; margin-bottom: 16px; }
    .grid-2 .col { display: table-cell; width: 48%; vertical-align: top; }
    .grid-2 .gap { display: table-cell; width: 4%; }
    .box-title { font-size: 11px; font-weight: bold; margin: 0 0 6px; }
    .box { border: 1px solid #333; padding: 9px; font-size: 10.5px; min-height: 75px; }
    .qr-box { text-align: center; }
    .qr-box img { width: 100px; height: 100px; }

    .ttd-grid { display: table; width: 100%; margin-top: 12px; }
    .ttd-col { display: table-cell; width: 33.3%; text-align: center; font-size: 10.5px; vertical-align: top; }
    .ttd-space { height: 48px; }
    .ttd-nama { font-weight: bold; text-decoration: underline; }

    .bar-atas { background: #1a1a1a; height: 5px; margin-bottom: 10px; }
    .kop { display: table; width: 100%; margin-bottom: 6px; }
    .kop-row { display: table-row; }
    .kop-cell { display: table-cell; vertical-align: middle; }
    .kop-logo { width: 90px; text-align: center; }
    .kop-logo img { max-width: 85px; max-height: 85px; }
    .kop-text { text-align: center; font-family: 'DejaVu Serif', serif; }
    .kop-text h1 { font-size: 15px; margin: 0; font-weight: bold; text-transform: none; }
    .kop-text h2 { font-size: 12.5px; margin: 1px 0; font-weight: normal; }
    .kop-text h3 { font-size: 17px; margin: 2px 0; font-weight: bold; }
    .kop-text p { font-size: 10.5px; margin: 1px 0; }
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

@if($sekolah->rapor_pakai_header_custom && $sekolah->rapor_header_custom)
<div style="text-align:center;margin-bottom:10px;">
    <img src="{{ public_path('storage/' . $sekolah->rapor_header_custom) }}" style="width:{{ $sekolah->rapor_header_custom_scale ?? 100 }}%;">
</div>
<div class="garis-tebal"></div>
@else
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
@endif

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

<div class="nilai-wrap">
    <div class="nilai-row nilai-head-1">
        <div class="c-komponen">Komponen</div>
        <div class="c-penilaian">Penilaian</div>
    </div>
    <div class="nilai-row nilai-head-2">
        <div class="c-no">No</div>
        <div class="c-mapel">Mata Pelajaran</div>
        <div class="c-tp">TP 1</div>
        <div class="c-tp">TP 2</div>
        <div class="c-tp">TP 3</div>
        <div class="c-tp">TP 4</div>
        <div class="c-sts">STS</div>
    </div>
    @forelse($rows as $i => $r)
    <div class="nilai-row">
        <div class="c-no">{{ $i + 1 }}</div>
        <div class="c-mapel c-mapel-body">{{ $r['mapel']->nama }}</div>
        @for($k = 0; $k < 4; $k++)
        <div class="c-tp c-nilai-angka">{{ $r['per_tp'][$k] ?? '-' }}</div>
        @endfor
        <div class="c-sts c-nilai-angka c-sts-angka">{{ $r['sts'] ?? '-' }}</div>
    </div>
    @empty
    <div class="nilai-row"><div style="width:100%;padding:10px;text-align:center;">Belum ada data penilaian.</div></div>
    @endforelse
</div>

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
        {{ $sekolah->kecamatan ?? '' }}, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>Wali Kelas
        <div class="ttd-space"></div>
        <div class="ttd-nama">{{ $waliKelas->nama ?? '-' }}</div>
        <div>{{ $waliKelas && $waliKelas->nip_nuptk ? 'NIP ' . $waliKelas->nip_nuptk : '' }}</div>
    </div>
</div>

</body>
</html>
