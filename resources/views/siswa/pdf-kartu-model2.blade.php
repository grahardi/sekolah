<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { width:242.65pt; height:153.07pt; }
    .kartu { width:100%; height:100%; position:relative; border-radius:10px; overflow:hidden; background:#fff; border:2px solid #FBBF24; }
    .top-strip { background:#FBBF24; height:6pt; }
    .header { text-align:center; padding:6px 10px 2px; }
    .header .sekolah-nama { font-size:8.5px; font-weight:bold; color:#1E3A5F; }
    .header .label { font-size:6px; color:#64748b; letter-spacing:1px; }
    .body { display:flex; padding:6px 10px; gap:8px; align-items:center; }
    .foto { width:50pt; height:60pt; border-radius:6px; overflow:hidden; background:#e2e8f0; flex-shrink:0; border:2px solid #FBBF24; }
    .foto img { width:100%; height:100%; object-fit:cover; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E3A5F; }
    .info .kelas { display:inline-block; background:#1E3A5F; color:#fff; font-size:7px; font-weight:bold; padding:1px 7px; border-radius:8px; margin:3px 0 4px; }
    .info p { font-size:6.5px; color:#475569; margin:1.5px 0; }
    .info p strong { color:#1E293B; }
    .barcode-area { position:absolute; bottom:5px; left:10px; right:10px; text-align:center; border-top:1px dashed #cbd5e1; padding-top:3px; }
    .barcode-area img { width:100%; height:20pt; }
    .barcode-area .kode { font-size:6.5px; letter-spacing:1px; color:#1E293B; font-weight:bold; }
</style>
</head>
<body>
<div class="kartu">
    <div class="top-strip"></div>
    <div class="header">
        <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p>
        <p class="label">KARTU TANDA PELAJAR</p>
    </div>
    <div class="body">
        <div class="foto"><img src="{{ $siswa->foto_url }}"></div>
        <div class="info">
            <h1>{{ $siswa->nama_lengkap }}</h1>
            <div class="kelas">{{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</div>
            <p><strong>NIS/NISN:</strong> {{ $siswa->nis }}/{{ $siswa->nisn }}</p>
            <p><strong>TTL:</strong> {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</p>
        </div>
    </div>
    <div class="barcode-area">
        @if($barcodePng)<img src="{{ $barcodePng }}">@endif
        <p class="kode">{{ $siswa->nis ?: $siswa->nisn }}</p>
    </div>
</div>
</body>
</html>
