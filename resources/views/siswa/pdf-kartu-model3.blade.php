<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { width:242.65pt; height:153.07pt; }
    .kartu { width:100%; height:100%; position:relative; border-radius:8px; overflow:hidden; background:#fff; border:1px solid #e2e8f0; }
    .side-bar { position:absolute; left:0; top:0; bottom:0; width:6pt; background:#1E3A5F; }
    .content { padding:8px 10px 8px 16px; }
    .header .sekolah-nama { font-size:8px; font-weight:bold; color:#1E293B; letter-spacing:.3px; }
    .header .label { font-size:6px; color:#94a3b8; letter-spacing:1px; margin-top:1px; }
    .divider { border-top:1px solid #f1f5f9; margin:5px 0; }
    .body { display:flex; gap:8px; }
    .foto { width:48pt; height:58pt; border-radius:4px; overflow:hidden; background:#f1f5f9; flex-shrink:0; }
    .foto img { width:100%; height:100%; object-fit:cover; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E293B; }
    .info .kelas { font-size:7px; color:#2563EB; font-weight:bold; margin:2px 0 4px; }
    .info p { font-size:6.3px; color:#64748b; margin:1.5px 0; }
    .info p strong { color:#1E293B; }
    .barcode-area { position:absolute; bottom:5px; left:16px; right:10px; text-align:center; }
    .barcode-area img { width:100%; height:20pt; }
    .barcode-area .kode { font-size:6.3px; letter-spacing:1px; color:#334155; font-weight:bold; }
</style>
</head>
<body>
<div class="kartu">
    <div class="side-bar"></div>
    <div class="content">
        <div class="header">
            <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p>
            <p class="label">KARTU TANDA PELAJAR</p>
        </div>
        <div class="divider"></div>
        <div class="body">
            <div class="foto"><img src="{{ $siswa->foto_url }}"></div>
            <div class="info">
                <h1>{{ $siswa->nama_lengkap }}</h1>
                <p class="kelas">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
                <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
            </div>
        </div>
    </div>
    <div class="barcode-area">
        @if($barcodePng)<img src="{{ $barcodePng }}">@endif
        <p class="kode">{{ $siswa->nis ?: $siswa->nisn }}</p>
    </div>
</div>
</body>
</html>
