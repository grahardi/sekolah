<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'DejaVu Sans', sans-serif; }
    body { width:242.65pt; height:153.07pt; }
    .kartu { width:242.65pt; height:153.07pt; border:2px solid #FBBF24; border-radius:10px; overflow:hidden; background:#fff; }
    .top-strip { background:#FBBF24; height:6pt; }
    .header { text-align:center; padding:6px 10px 2px; }
    .header .sekolah-nama { font-size:8.5px; font-weight:bold; color:#1E3A5F; }
    .header .label { font-size:6px; color:#64748b; letter-spacing:1px; }
    .body-table { width:100%; }
    .foto-cell { width:58pt; padding:6px 0 0 10px; vertical-align:top; }
    .foto { width:50pt; height:60pt; border-radius:6px; overflow:hidden; background:#e2e8f0; border:2px solid #FBBF24; }
    .foto img { width:50pt; height:60pt; }
    .info-cell { padding:6px 10px 0 8px; vertical-align:top; }
    .info h1 { font-size:9.5px; font-weight:bold; color:#1E3A5F; }
    .info .kelas { background:#1E3A5F; color:#fff; font-size:7px; font-weight:bold; padding:1px 7px; border-radius:8px; }
    .info p { font-size:6.5px; color:#475569; margin-top:2.5px; }
    .info p strong { color:#1E293B; }
    .barcode-wrap { padding:4pt 10pt 0; text-align:center; border-top:1px dashed #cbd5e1; margin:4pt 10pt 0; }
    .barcode-wrap img { width:210pt; height:18pt; }
    .barcode-wrap .kode { font-size:6.5px; letter-spacing:1px; color:#1E293B; font-weight:bold; }
</style>
</head>
<body>
<table class="kartu" cellpadding="0" cellspacing="0">
    <tr><td>
        <div class="top-strip"></div>
        <div class="header">
            <p class="sekolah-nama">{{ strtoupper($siswa->sekolah->nama ?? 'SEKOLAH') }}</p>
            <p class="label">KARTU TANDA PELAJAR</p>
        </div>

        <table class="body-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="foto-cell"><div class="foto"><img src="{{ $siswa->foto_url }}"></div></td>
                <td class="info-cell">
                    <div class="info">
                        <h1>{{ $siswa->nama_lengkap }}</h1>
                        <p><span class="kelas">{{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</span></p>
                        <p><strong>NIS/NISN:</strong> {{ $siswa->nis }}/{{ $siswa->nisn }}</p>
                        <p><strong>TTL:</strong> {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <div class="barcode-wrap">
            @if($barcodePng)<img src="{{ $barcodePng }}"><br>@endif
            <span class="kode">{{ $siswa->nis ?: $siswa->nisn }}</span>
        </div>
    </td></tr>
</table>
</body>
</html>
