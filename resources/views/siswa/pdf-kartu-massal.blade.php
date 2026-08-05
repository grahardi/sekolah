<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { padding:14pt; }
    .grid { display:flex; flex-wrap:wrap; gap:8pt; }
    .kartu { width:242.65pt; height:153.07pt; position:relative; border-radius:8px; overflow:hidden; background:#fff; page-break-inside:avoid;
        @if($model == 1) border:1px solid #cbd5e1;
        @elseif($model == 2) border:2px solid #FBBF24;
        @else border:1px solid #e2e8f0;
        @endif
    }
    .header-navy { background:linear-gradient(135deg,#1E3A5F,#2563EB); color:#fff; padding:8px 10px; display:flex; align-items:center; gap:6px; }
    .header-navy .sekolah-nama { font-size:8px; font-weight:bold; }
    .header-navy .label { font-size:6px; opacity:.85; }
    .top-strip { background:#FBBF24; height:6pt; }
    .header-badge { text-align:center; padding:6px 10px 2px; }
    .header-badge .sekolah-nama { font-size:8.5px; font-weight:bold; color:#1E3A5F; }
    .header-badge .label { font-size:6px; color:#64748b; letter-spacing:1px; }
    .side-bar { position:absolute; left:0; top:0; bottom:0; width:6pt; background:#1E3A5F; }
    .content-min { padding:8px 10px 8px 16px; }
    .content-min .sekolah-nama { font-size:8px; font-weight:bold; color:#1E293B; }
    .content-min .label { font-size:6px; color:#94a3b8; letter-spacing:1px; margin-top:1px; }
    .body { display:flex; padding:6px 10px; gap:8px; align-items:center; }
    .foto { border-radius:4px; overflow:hidden; background:#e2e8f0; flex-shrink:0; }
    .foto img { width:100%; height:100%; object-fit:cover; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E293B; }
    .kelas-navy { display:inline-block; background:#dbeafe; color:#1E3A5F; font-size:7px; font-weight:bold; padding:1px 6px; border-radius:8px; margin:2px 0 4px; }
    .kelas-badge { display:inline-block; background:#1E3A5F; color:#fff; font-size:7px; font-weight:bold; padding:1px 7px; border-radius:8px; margin:3px 0 4px; }
    .kelas-min { font-size:7px; color:#2563EB; font-weight:bold; margin:2px 0 4px; }
    .info p { font-size:6.3px; color:#475569; margin:1.5px 0; }
    .info p strong { color:#1E293B; }
    .barcode-area { position:absolute; bottom:5px; left:10px; right:10px; text-align:center; }
    .barcode-area img { width:100%; height:20pt; }
    .barcode-area .kode { font-size:6.3px; letter-spacing:1px; color:#1E293B; font-weight:bold; }
</style>
</head>
<body>
<div class="grid">
    @foreach($siswaList as $siswa)
    <div class="kartu">
        @if($model == 1)
            <div class="header-navy">
                <div style="font-size:12px;">🎓</div>
                <div><p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p></div>
            </div>
            <div class="body">
                <div class="foto" style="width:50pt;height:60pt;"><img src="{{ $siswa->foto_url }}"></div>
                <div class="info">
                    <h1>{{ $siswa->nama_lengkap }}</h1>
                    <div class="kelas-navy">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</div>
                    <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                    <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                </div>
            </div>
        @elseif($model == 2)
            <div class="top-strip"></div>
            <div class="header-badge"><p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p></div>
            <div class="body">
                <div class="foto" style="width:48pt;height:56pt;border:2px solid #FBBF24;"><img src="{{ $siswa->foto_url }}"></div>
                <div class="info">
                    <h1>{{ $siswa->nama_lengkap }}</h1>
                    <div class="kelas-badge">{{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</div>
                    <p><strong>NIS/NISN:</strong> {{ $siswa->nis }}/{{ $siswa->nisn }}</p>
                </div>
            </div>
        @else
            <div class="side-bar"></div>
            <div class="content-min">
                <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p>
                <div class="body" style="padding:5px 0 0;">
                    <div class="foto" style="width:46pt;height:54pt;"><img src="{{ $siswa->foto_url }}"></div>
                    <div class="info">
                        <h1>{{ $siswa->nama_lengkap }}</h1>
                        <p class="kelas-min">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
                        <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                        <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                    </div>
                </div>
            </div>
        @endif
        <div class="barcode-area">
            @if($siswa->barcode_png ?? null)<img src="{{ $siswa->barcode_png }}">@endif
            <p class="kode">{{ $siswa->nis ?: $siswa->nisn }}</p>
        </div>
    </div>
    @endforeach
</div>
</body>
</html>
