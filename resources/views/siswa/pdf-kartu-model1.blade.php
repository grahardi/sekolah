<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { width:242.65pt; height:153.07pt; }
    .kartu { width:100%; height:100%; position:relative; border:1px solid #cbd5e1; border-radius:10px; overflow:hidden; background:#fff; }
    .header { background:linear-gradient(135deg,#1E3A5F,#2563EB); color:#fff; padding:8px 10px; display:flex; align-items:center; gap:6px; }
    .header .sekolah-nama { font-size:8px; font-weight:bold; line-height:1.1; }
    .header .label { font-size:6px; opacity:.85; }
    .body { display:flex; padding:8px 10px; gap:8px; }
    .foto { width:52pt; height:64pt; border-radius:4px; overflow:hidden; background:#e2e8f0; flex-shrink:0; border:1px solid #cbd5e1; }
    .foto img { width:100%; height:100%; object-fit:cover; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E293B; }
    .info .kelas { display:inline-block; background:#dbeafe; color:#1E3A5F; font-size:7px; font-weight:bold; padding:1px 6px; border-radius:8px; margin:2px 0 4px; }
    .info p { font-size:6.5px; color:#475569; margin:1px 0; }
    .info p strong { color:#1E293B; }
    .barcode-area { position:absolute; bottom:6px; left:10px; right:10px; text-align:center; }
    .barcode-area img { width:100%; height:22pt; }
    .barcode-area .kode { font-size:6.5px; letter-spacing:1px; color:#1E293B; font-weight:bold; }
</style>
</head>
<body>
<div class="kartu">
    <div class="header">
        <div style="font-size:14px;">🎓</div>
        <div>
            <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p>
            <p class="label">KARTU TANDA PELAJAR</p>
        </div>
    </div>
    <div class="body">
        <div class="foto"><img src="{{ $siswa->foto_url }}"></div>
        <div class="info">
            <h1>{{ $siswa->nama_lengkap }}</h1>
            <div class="kelas">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</div>
            <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
            <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
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
