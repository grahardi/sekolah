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
</style>
</head>
<body>

@php $sekolahNama = strtoupper($sekolah->nama ?? '-'); @endphp

<h1>Laporan Hasil Belajar Tengah Semester</h1>
<h2>{{ $sekolahNama }} &middot; Tahun Pelajaran {{ $tahunAktif->nama }}</h2>

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
