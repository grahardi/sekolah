<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { width:242.65pt; height:153.07pt; }
    .outer-table { width:242.65pt; height:153.07pt; }
    .side-cell { width:6pt; background:#1E3A5F; }
    .main-cell { border:1px solid #e2e8f0; border-left:none; vertical-align:top; padding:8px 10px; }
    .header .sekolah-nama { font-size:8px; font-weight:bold; color:#1E293B; letter-spacing:.3px; }
    .header .label { font-size:6px; color:#94a3b8; letter-spacing:1px; margin-top:1px; }
    .divider { border-top:1px solid #f1f5f9; margin:5px 0; }
    .body-table { width:100%; }
    .foto-cell { width:56pt; vertical-align:top; }
    .foto { width:48pt; height:58pt; border-radius:4px; overflow:hidden; background:#f1f5f9; }
    .foto img { width:48pt; height:58pt; }
    .info-cell { padding-left:8px; vertical-align:top; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E293B; }
    .info .kelas { font-size:7px; color:#2563EB; font-weight:bold; }
    .info p { font-size:6.3px; color:#64748b; margin-top:2px; }
    .info p strong { color:#1E293B; }
    .barcode-wrap { padding-top:4pt; text-align:center; }
    .barcode-wrap img { width:200pt; height:18pt; }
    .barcode-wrap .kode { font-size:6.3px; letter-spacing:1px; color:#334155; font-weight:bold; }
</style>
</head>
<body>
<table class="outer-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="side-cell"></td>
        <td class="main-cell">
            <div class="header">
                <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p>
                <p class="label">KARTU TANDA PELAJAR</p>
            </div>
            <div class="divider"></div>

            <table class="body-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="foto-cell"><div class="foto"><img src="{{ $siswa->foto_url }}"></div></td>
                    <td class="info-cell">
                        <div class="info">
                            <h1>{{ $siswa->nama_lengkap }}</h1>
                            <p class="kelas">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
                            <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                            <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="barcode-wrap">
                @if($barcodePng)<img src="{{ $barcodePng }}"><br>@endif
                <span class="kode">{{ $siswa->nis ?: $siswa->nisn }}</span>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
