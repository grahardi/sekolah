<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { padding:14pt; }
    .kartu-wrap { display:inline-block; width:242.65pt; margin:0 6pt 10pt 0; }
    .kartu { width:242.65pt; height:153.07pt; border-radius:8px; overflow:hidden; background:#fff; }
    .kartu.m1 { border:1px solid #cbd5e1; }
    .kartu.m2 { border:2px solid #FBBF24; }
    .kartu.m3 { border:1px solid #e2e8f0; }
    .header-navy { background:#1E3A5F; color:#fff; padding:7px 10px; }
    .header-navy .sekolah-nama { font-size:8px; font-weight:bold; }
    .header-navy .label { font-size:6px; opacity:.85; }
    .top-strip { background:#FBBF24; height:6pt; }
    .header-badge { text-align:center; padding:6px 10px 2px; }
    .header-badge .sekolah-nama { font-size:8.5px; font-weight:bold; color:#1E3A5F; }
    .header-badge .label { font-size:6px; color:#64748b; letter-spacing:1px; }
    .outer-table-min { width:100%; }
    .side-cell { width:6pt; background:#1E3A5F; }
    .main-cell-min { padding:8px 10px; vertical-align:top; }
    .content-min .sekolah-nama { font-size:8px; font-weight:bold; color:#1E293B; }
    .content-min .label { font-size:6px; color:#94a3b8; letter-spacing:1px; margin-top:1px; }
    .body-table { width:100%; }
    .foto-cell { vertical-align:top; padding-left:10px; }
    .foto { border-radius:4px; overflow:hidden; background:#e2e8f0; }
    .info-cell { padding:0 10px 0 8px; vertical-align:top; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E293B; }
    .kelas-navy { background:#dbeafe; color:#1E3A5F; font-size:7px; font-weight:bold; padding:1px 6px; border-radius:8px; }
    .kelas-badge { background:#1E3A5F; color:#fff; font-size:7px; font-weight:bold; padding:1px 7px; border-radius:8px; }
    .kelas-min { font-size:7px; color:#2563EB; font-weight:bold; }
    .info p { font-size:6.3px; color:#475569; margin-top:2px; }
    .info p strong { color:#1E293B; }
    .barcode-wrap { padding:4pt 10pt 0; text-align:center; }
    .barcode-wrap img { width:210pt; height:18pt; }
    .barcode-wrap .kode { font-size:6.3px; letter-spacing:1px; color:#1E293B; font-weight:bold; }
</style>
</head>
<body>
    @foreach($siswaList as $siswa)
    <div class="kartu-wrap">
    @if($model == 1)
        <table class="kartu m1" cellpadding="0" cellspacing="0"><tr><td>
            <div class="header-navy"><p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p></div>
            <table class="body-table" cellpadding="0" cellspacing="0"><tr>
                <td class="foto-cell" style="width:60pt;padding-top:8px;"><div class="foto" style="width:52pt;height:64pt;border:1px solid #cbd5e1;"><img src="{{ $siswa->foto_url }}" style="width:52pt;height:64pt;"></div></td>
                <td class="info-cell" style="padding-top:8px;">
                    <div class="info">
                        <h1>{{ $siswa->nama_lengkap }}</h1>
                        <p><span class="kelas-navy">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</span></p>
                        <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                        <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                    </div>
                </td>
            </tr></table>
            <div class="barcode-wrap">@if($siswa->barcode_png ?? null)<img src="{{ $siswa->barcode_png }}"><br>@endif<span class="kode">{{ $siswa->nis ?: $siswa->nisn }}</span></div>
        </td></tr></table>
    @elseif($model == 2)
        <table class="kartu m2" cellpadding="0" cellspacing="0"><tr><td>
            <div class="top-strip"></div>
            <div class="header-badge"><p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p></div>
            <table class="body-table" cellpadding="0" cellspacing="0"><tr>
                <td class="foto-cell" style="width:58pt;padding-top:6px;"><div class="foto" style="width:50pt;height:60pt;border:2px solid #FBBF24;border-radius:6px;"><img src="{{ $siswa->foto_url }}" style="width:50pt;height:60pt;"></div></td>
                <td class="info-cell" style="padding-top:6px;">
                    <div class="info">
                        <h1>{{ $siswa->nama_lengkap }}</h1>
                        <p><span class="kelas-badge">{{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</span></p>
                        <p><strong>NIS/NISN:</strong> {{ $siswa->nis }}/{{ $siswa->nisn }}</p>
                    </div>
                </td>
            </tr></table>
            <div class="barcode-wrap">@if($siswa->barcode_png ?? null)<img src="{{ $siswa->barcode_png }}"><br>@endif<span class="kode">{{ $siswa->nis ?: $siswa->nisn }}</span></div>
        </td></tr></table>
    @else
        <table class="outer-table-min kartu m3" cellpadding="0" cellspacing="0"><tr>
            <td class="side-cell"></td>
            <td class="main-cell-min">
                <div class="content-min"><p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p><p class="label">KARTU TANDA PELAJAR</p></div>
                <table class="body-table" cellpadding="0" cellspacing="0"><tr>
                    <td class="foto-cell" style="width:54pt;padding-top:5px;"><div class="foto" style="width:46pt;height:54pt;"><img src="{{ $siswa->foto_url }}" style="width:46pt;height:54pt;"></div></td>
                    <td class="info-cell" style="padding-top:5px;">
                        <div class="info">
                            <h1>{{ $siswa->nama_lengkap }}</h1>
                            <p class="kelas-min">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
                            <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                            <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                        </div>
                    </td>
                </tr></table>
                <div class="barcode-wrap">@if($siswa->barcode_png ?? null)<img src="{{ $siswa->barcode_png }}"><br>@endif<span class="kode">{{ $siswa->nis ?: $siswa->nisn }}</span></div>
            </td>
        </tr></table>
    @endif
    </div>
    @endforeach
</body>
</html>
